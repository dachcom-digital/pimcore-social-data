pimcore.registerNS('SocialData.SettingsPanel');
SocialData.SettingsPanel = Class.create({

    panel: null,

    initialize: function () {
        this.buildLayout();
    },

    getConfig: function () {
        return this.config;
    },

    buildLayout: function () {

        var pimcoreSystemPanel = Ext.getCmp('pimcore_panel_tabs');

        if (this.panel) {
            return this.panel;
        }

        this.panel = new Ext.Panel({
            id: 'social_data_bundle_settings',
            title: t('social_data.settings.configuration'),
            border: false,
            iconCls: 'social_data_icon_config',
            layout: 'border',
            closable: true,
            tbar: [
                {
                    type: 'button',
                    text: t('social_data.logs.flush_all'),
                    iconCls: 'pimcore_icon_cleanup',
                    handler: function (btn) {
                        Ext.Msg.confirm(t('social_data.logs.flush_confirm_title'), t('social_data.logs.flush_confirm'), function (confirmBtn) {

                            if (confirmBtn !== 'yes') {
                                return;
                            }

                            btn.setDisabled(true);

                            Ext.Ajax.request({
                                method: 'DELETE',
                                url: '/admin/social-data/logs/flush',
                                success: function (response) {
                                    btn.setDisabled(false);
                                    var resp = Ext.decode(response.responseText);
                                    if (resp.success === true) {
                                        Ext.Msg.alert(t('success'), t('social_data.logs.flush_success'));
                                    } else {
                                        Ext.Msg.alert(t('error'), resp.message);
                                    }
                                }.bind(this)
                            });
                        }.bind(this));
                    }.bind(this)
                }
            ]
        });

        this.panel.on('destroy', function () {
            pimcore.globalmanager.remove('social_data_bundle_settings');
        }.bind(this));

        pimcoreSystemPanel.add(this.panel);
        pimcoreSystemPanel.setActiveItem('social_data_bundle_settings');

        this.generateDataClassHealthCheck();
        this.addConnectors();
    },

    generateDataClassHealthCheck: function () {

        Ext.Ajax.request({
            url: '/admin/social-data/settings/data-class-health-check',
            success: function (response) {
                var config = Ext.decode(response.responseText);

                var descriptionText = !config.dataClassReady
                    ? ' ' + t(' social_data.settings.dataclass.not_ready').format(config.dataClassPath)
                    : ' ' + t('social_data.settings.dataclass.active_data_class').format(config.dataClassPath);

                this.panel.add({
                    region: 'north',
                    xtype: 'fieldcontainer',
                    layout: 'fit',
                    style: 'margin: 10px',
                    items: [
                        {
                            xtype: 'label',
                            text: t('social_data.settings.dataclass.configuration') + ': ',
                        },
                        {
                            xtype: 'label',
                            text: config.dataClassReady ? t('social_data.settings.dataclass.ready_tag') : t('social_data.settings.dataclass.not_ready_tag'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', config.dataClassReady ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            }
                        },
                        {
                            xtype: 'label',
                            text: descriptionText
                        }
                    ]
                })
            }.bind(this)
        });
    },

    addConnectors: function () {
        Ext.Ajax.request({
            url: '/admin/social-data/settings/get-connectors',
            success: this.buildConnectors.bind(this)
        });
    },

    buildConnectors: function (response) {

        var connectorConfig = Ext.decode(response.responseText);

        this.tabPanel = new Ext.TabPanel({
            title: t('social_data.connector.list'),
            closable: false,
            deferredRender: false,
            forceLayout: true,
            layout: 'fit',
            region: 'center',
            style: 'padding: 10px',
        });

        this.panel.add(this.tabPanel);

        Ext.Array.each(connectorConfig.connectors, function (connector) {

            var connectorLayout, connectorPanel;

            if (!SocialData.Connector.hasOwnProperty(connector.name)) {

                connectorLayout = new SocialData.Connector[ucfirst(connector.name)](connector.name, connector.config);

                connectorPanel = new Ext.Panel({
                    title: connector.label,
                    autoScroll: true,
                    forceLayout: true,
                    border: false,
                    items: [connectorLayout.getSystemFields()]
                });

                if (connectorLayout.hasCustomConfiguration() === true) {
                    connectorPanel.add(connectorLayout.generateCustomConfigurationPanel());
                }

                this.tabPanel.add(connectorPanel);
            }

        }.bind(this));

        this.tabPanel.setActiveTab(0);
    },

    activate: function () {
        Ext.getCmp('pimcore_panel_tabs').setActiveItem('social_data_bundle_settings');
    }
});