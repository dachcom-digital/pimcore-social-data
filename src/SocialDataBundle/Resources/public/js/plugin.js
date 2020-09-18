pimcore.registerNS('pimcore.plugin.SocialData');

pimcore.plugin.SocialData = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return 'pimcore.plugin.SocialData';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        if (!String.prototype.format) {
            String.prototype.format = function () {
                var args = arguments;
                return this.replace(/{(\d+)}/g, function (match, number) {
                    return typeof args[number] != 'undefined'
                        ? args[number]
                        : match
                        ;
                });
            };
        }
    },

    uninstall: function () {
        // void
    },

    pimcoreReady: function (params, broker) {

        var socialDataMenu, user = pimcore.globalmanager.get('user');

        if (!user.isAllowed('social_data_permission_settings')) {
            return false;
        }

        socialDataMenu = new Ext.Action({
            id: 'social_data_bundle_setting_button',
            text: t('social_data.settings.configuration'),
            iconCls: 'social_data_icon_bundle',
            handler: this.openSettingsPanel.bind(this)
        });

        if (layoutToolbar.settingsMenu) {
            layoutToolbar.settingsMenu.add(socialDataMenu);
        }
    },

    openSettingsPanel: function () {
        try {
            pimcore.globalmanager.get('social_data_bundle_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('social_data_bundle_settings', new SocialData.SettingsPanel());
        }
    }

});

new pimcore.plugin.SocialData();
