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

        var subMenu = [],
            user = pimcore.globalmanager.get('user');

        if (user.isAllowed('social_data_permission_settings')) {
            subMenu.push(new Ext.Action({
                id: 'social_data_bundle_setting_button',
                text: t('social_data.settings.configuration'),
                iconCls: 'social_data_icon_config',
                handler: this.openSettingsPanel.bind(this)
            }));
        }


        if (user.isAllowed('social_data_permission_walls')) {
            subMenu.push(new Ext.Action({
                id: 'social_data_bundle_walls_button',
                text: t('social_data.settings.walls'),
                iconCls: 'social_data_icon_walls',
                handler: this.openWallsPanel.bind(this)
            }));
        }

        if (layoutToolbar.settingsMenu && subMenu.length > 0) {
            layoutToolbar.settingsMenu.add({
                text: t('social_data.settings.menu'),
                iconCls: 'social_data_icon_bundle',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: subMenu
                }
            });
        }
    },

    openSettingsPanel: function () {
        try {
            pimcore.globalmanager.get('social_data_bundle_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('social_data_bundle_settings', new SocialData.SettingsPanel());
        }
    },

    openWallsPanel: function () {
        try {
            pimcore.globalmanager.get('social_data_bundle_walls').activate();
        } catch (e) {
            pimcore.globalmanager.add('social_data_bundle_walls', new SocialData.WallsPanel());
        }
    }

});

new pimcore.plugin.SocialData();
