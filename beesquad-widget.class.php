<?php

namespace Fhyve\Beesquad\WordpressWidget;

class Plugin
{
    /**
     * Path to the plugin entrypoint file
     *
     * @var string
     */
    private $pluginEntryPoint;

    /**
     * @param string $pluginEntryPoint path to the plugin entrypoint file
     */
    public function __construct($pluginEntryPoint)
    {
        $this->pluginEntryPoint = $pluginEntryPoint;
    }

    /**
     * Register the BeeSquad plugin
     */
    public function register()
    {
        add_action("wp_footer", [$this, "printWidget"], 10);
        add_action("admin_menu", [$this, "registerMenu"]);
        add_action("admin_init", [$this, "registerSettings"]);

        register_deactivation_hook($this->pluginEntryPoint, [$this, "deactivationCallback"]);
    }

    public function registerMenu()
    {
        add_options_page(
            "BeeSquad",
            "BeeSquad - Live Chat",
            "administrator",
            "beesquad-settings",
            [$this, "printSettingsPage"],
            "dashicons-admin-generic"
        );
    }

    public function printSettingsPage()
    {
        echo <<<HTML
<div class="wrap">
    <h2>Configure your BeeSquad Widget</h2>
    <form method="post" action="options.php">
HTML;

        settings_fields("beesquad-settings");
        do_settings_sections("beesquad-widget");
        submit_button();

        echo <<<HTML
    </form>
</div>
HTML;
    }

    public function printWidget()
    {
        $siteId = esc_attr(get_option("beesquad_site_id"));

        if (empty($siteId)) {
            return;
        }

        echo <<<HTML
<!-- BeeSquad widget -->
<beesquad-widget
    site="$siteId"
></beesquad-widget>
<script
    src="https://sdk.beesquad.ch"
    type="text/javascript"
    charset="utf-8"
    async
></script>
<!-- BeeSquad widget -->
HTML;
    }

    public function registerSettings()
    {
        add_settings_section("beesquad-widget", "", null, "beesquad-widget");

        register_setting("beesquad-settings", "beesquad_site_id", [
            "type" => "string",
            "description" => "BeeSquad widget site id",
            "sanitize_callback" => [$this, "validateSiteId"],
        ]);

        add_settings_field("beesquad_site_id", "Site Id", function () {
            $siteId = esc_attr(get_option("beesquad_site_id"));

            echo <<<HTML
                <input
                    type="text"
                    name="beesquad_site_id"
                    placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                    value="$siteId"
                    pattern="^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$"
                />
HTML;
        }, "beesquad-widget", "beesquad-widget");
    }

    public function validateSiteId($input)
    {
        $oldValue = get_option("beesquad_site_id");

        if (empty($input) ||
            preg_match("/^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$/", $input)
        ) {
            return $input;
        }

        add_settings_error(
            "beesquad_site_id",
            "invalid-beesquad-site-id",
            "\"".esc_html($input)."\" is not a valid BeeSquad site id"
        );

        return $oldValue;
    }

    public function deactivationCallback()
    {
        delete_option("beesquad_site_id");
    }
}
