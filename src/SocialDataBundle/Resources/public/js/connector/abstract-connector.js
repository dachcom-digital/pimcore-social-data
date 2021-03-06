pimcore.registerNS('SocialData.Connector.AbstractConnector');
SocialData.Connector.AbstractConnector = Class.create({

    type: null,
    data: null,
    customConfiguration: null,
    customConfigurationPanel: null,

    states: {
        installation: {
            identifier: 'installed',
            activate: t('social_data.connector.install'),
            activated: t('social_data.connector.installed'),
            inactivate: t('social_data.connector.uninstall'),
            inactivated: t('Not Installed')
        },
        availability: {
            identifier: 'enabled',
            activate: t('social_data.connector.enable'),
            activated: t('social_data.connector.enabled'),
            inactivate: t('social_data.connector.disable'),
            inactivated: t('social_data.connector.disabled')
        },
        connection: {
            identifier: 'connected',
            activate: t('social_data.connector.connect'),
            activated: t('social_data.connector.connected'),
            inactivate: t('social_data.connector.disconnect'),
            inactivated: t('social_data.connector.not_connected')
        },
    },

    initialize: function (type, data) {
        this.type = type;
        this.data = data;
        this.customConfiguration = this.data.customConfiguration !== null
            ? this.data.customConfiguration
            : {}
    },

    /**
     * @abstract
     */
    hasCustomConfiguration: function () {
        return false;
    },

    /**
     * @abstract
     *
     * connectHandler: Just a proxy
     * to allow connectors overriding connecting process!
     */
    connectHandler: function (stateType, btn) {
        this.stateHandler(stateType, btn);
    },

    /**
     * @abstract
     */
    getCustomConfigurationFields: function () {
        return [];
    },

    /**
     * @abstract
     */
    beforeDisableFieldState: function (stateType, toDisableState) {
        return toDisableState;
    },

    /**
     * @abstract
     */
    afterSaveCustomConfiguration: function (resp) {
        return null;
    },

    /**
     * @abstract
     */
    afterChangeState: function (stateType, active) {
        return null;
    },

    afterInstall: function () {

        if (this.hasCustomConfiguration() === false) {
            return;
        }

        this.customConfigurationPanel.setDisabled(false);
    },

    afterUninstall: function () {

        if (this.hasCustomConfiguration() === false) {
            return;
        }

        Ext.each(this.customConfigurationPanel.getForm().getFields().items, function (field) {
            field.setValue(null);
        });

        this.customConfiguration = {};
        this.customConfigurationPanel.setDisabled(true);
    },

    getType: function () {
        return this.type;
    },

    isInstalled: function () {
        return this.data && this.data.installed === true;
    },

    refreshCustomConfigurationPanel: function () {

        if (this.hasCustomConfiguration() === false) {
            return;
        }

        this.customConfigurationPanel.removeAll();
        this.customConfigurationPanel.setLoading(true);

        Ext.Ajax.request({
            url: '/admin/social-data/settings/get-connector/' + this.getType(),
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                this.customConfigurationPanel.setLoading(false);

                if (resp.success === false) {
                    Ext.MessageBox.alert(t('error'), resp.message);
                    return;
                }

                if (resp.hasOwnProperty('connector') === false) {
                    Ext.MessageBox.alert(t('error'), 'No connector data found');
                    return;
                }

                if (resp.connector.config.hasOwnProperty('customConfiguration')) {
                    this.customConfiguration = resp.connector.config.customConfiguration;
                }

                this.customConfigurationPanel.add(this.getCustomConfigurationFields());

            }.bind(this)
        });
    },

    generateCustomConfigurationPanel: function () {

        var fieldset = new Ext.form.FieldSet({
            collapsible: false,
            title: t('social_data.connector.configuration')
        });

        this.customConfigurationPanel = new Ext.form.Panel({
            title: false,
            layout: 'form',
            border: false,
            autoScroll: true,
            width: 800,
            trackResetOnLoad: true,
            disabled: this.data.installed === false,
            items: this.getCustomConfigurationFields(),
            buttons: [
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_save',
                    handler: this.saveCustomConfiguration.bind(this)
                }
            ]
        });

        fieldset.add(this.customConfigurationPanel);

        return fieldset;
    },

    getSystemFields: function () {

        return {
            xtype: 'fieldset',
            collapsible: false,
            title: t('social_data.connector.system'),
            items: [
                {
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    cls: 'install-field-container state-installation-field-container',
                    items: [
                        {
                            xtype: 'label',
                            text: t('social_data.connector.installation') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.installed ? t('social_data.connector.installed') : t('social_data.connector.not_installed'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', this.data.installed ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            },
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            iconCls: this.data.installed ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.installed ? t('social_data.connector.uninstall') : t('social_data.connector.install'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    btn.setStyle('background-color', this.data.installed ? '#af1e32' : '#0e793e')
                                }.bind(this)
                            },
                            handler: this.installationHandler.bind(this)
                        },
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    disabled: this.beforeDisableFieldState('availability', !this.data.installed),
                    cls: 'state-field-container state-availability-field-container',
                    layout: 'hbox',
                    items: [
                        {
                            xtype: 'label',
                            text: t('social_data.connector.status') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.enabled ? t('social_data.connector.enabled') : t('social_data.connector.disabled'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', this.data.enabled ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            },
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            iconCls: this.data.enabled ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.enabled ? t('social_data.connector.disable') : t('social_data.connector.enable'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    btn.setStyle('background-color', this.data.enabled ? '#af1e32' : '#0e793e')
                                }.bind(this)
                            },
                            handler: this.stateHandler.bind(this, 'availability')
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    disabled: this.beforeDisableFieldState('connection', (!this.data.installed || this.data.autoConnect === true)),
                    cls: 'state-field-container ' + (this.data.autoConnect === false ? 'state-connection-field-container' : ''),
                    layout: 'hbox',
                    items: [
                        {
                            xtype: 'label',
                            text: t('social_data.connector.connection') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.autoConnect ? t('social_data.connector.auto_connected') + ': ' : (this.data.connected ? t('social_data.connector.connected') : t('social_data.connector.disconnected')),
                            listeners: {
                                afterrender: function (label) {
                                    var color = this.data.autoConnect ? '#212121' : (this.data.connected ? '#0e793e' : '#af1e32');
                                    label.setStyle('color', color);
                                }.bind(this)
                            }
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            hidden: this.data.autoConnect,
                            iconCls: this.data.connected ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.connected ? t('social_data.connector.disconnect') : t('social_data.connector.connect'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    var color = this.data.autoConnect ? '#505050' : (this.data.connected ? '#af1e32' : '#0e793e');
                                    btn.setStyle('background-color', color);
                                }.bind(this)
                            },
                            handler: this.connectHandler.bind(this, 'connection')
                        }
                    ]
                }
            ]
        }
    },

    installationHandler: function (btn) {

        var url = this.data.installed
            ? '/admin/social-data/settings/uninstall-connector/'
            : '/admin/social-data/settings/install-connector/',
            fieldset = btn.up('fieldset'),
            doRequest = function (btn) {

                btn.setDisabled(true);

                Ext.Ajax.request({
                    url: url + this.getType(),
                    success: function (response) {
                        var resp = Ext.decode(response.responseText);

                        btn.setDisabled(false);

                        if (resp.success === false) {
                            Ext.MessageBox.alert(t('error'), resp.message);
                            return;
                        }

                        this.data.installed = resp.installed;
                        this.data.token = resp.token;

                        if (this.data.installed === false) {
                            this.data.enabled = false;
                            this.data.connected = false;
                        }

                        this.changeState(fieldset, 'installation');
                        this.changeState(fieldset, 'availability');
                        this.changeState(fieldset, 'connection');

                        if (this.data.installed === true) {
                            this.afterInstall();
                        } else {
                            this.afterUninstall();
                        }

                    }.bind(this),
                    failure: function (response) {
                        btn.setDisabled(false);
                    }
                });
            }.bind(this);

        if (this.data.installed === false) {
            doRequest(btn);
            return;
        }

        Ext.Msg.confirm(t('delete'), t('social_data.connector.uninstall_note'), function (confirmBtn) {

            if (confirmBtn !== 'yes') {
                return;
            }

            doRequest(btn);
        });
    },

    stateHandler: function (stateType, btn) {

        var stateData = this.states[stateType],
            fieldset = btn.up('fieldset'), flag, url;

        flag = this.data[stateData.identifier] === true ? 'deactivate' : 'activate';
        url = '/admin/social-data/settings/change-connector-type/' + this.getType() + '/' + stateType + '/' + flag;

        btn.setDisabled(true);

        Ext.Ajax.request({
            url: url,
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                btn.setDisabled(false);

                if (resp.success === false) {
                    Ext.MessageBox.alert(t('error'), resp.message);
                    return;
                }

                this.data[stateData.identifier] = resp.stateMode === 'activated';

                this.changeState(fieldset, stateType)

            }.bind(this),
            failure: function (response) {
                btn.setDisabled(false);
            }
        });
    },

    changeState: function (fieldset, stateType) {

        var fieldContainer = fieldset.query('fieldcontainer[cls*="state-' + stateType + '-field-container"]')[0],
            stateLabelField = fieldContainer ? fieldContainer.query('label[cls*="state-field-label"]')[0] : null,
            btn = fieldContainer ? fieldContainer.query('button')[0] : null,
            stateData = this.states[stateType],
            active = this.data.installed === false ? false : this.data[stateData.identifier];

        if (stateLabelField !== null) {
            stateLabelField.setText(active ? stateData.activated : stateData.inactivated);
            stateLabelField.setStyle('color', active ? '#0e793e' : '#af1e32');
        }

        if (btn !== null) {
            btn.setText(active ? stateData.inactivate : stateData.activate);
            btn.setStyle('background-color', active ? '#af1e32' : '#0e793e');
            btn.setIconCls(active ? 'pimcore_icon_cancel' : 'pimcore_icon_add');
        }

        if (stateType === 'installation') {
            if (this.hasCustomConfiguration() === true) {
                this.customConfigurationPanel.setDisabled(!this.data.installed);
            }
        } else if (fieldContainer !== undefined) {
            fieldContainer.setDisabled(this.beforeDisableFieldState(stateType, !this.data.installed));
        }

        this.afterChangeState(stateType, active)
    },

    saveCustomConfiguration: function (btn) {

        var fieldset = btn.up('panel');

        if (this.data.installed === false) {
            return;
        }

        if (this.customConfigurationPanel.getForm().isValid() === false) {
            Ext.MessageBox.alert(t('error'), t('social_data.connector.save_incorrect_configuration'));
            return;
        }

        fieldset.setLoading(true);

        Ext.Ajax.request({
            url: '/admin/social-data/settings/save-connector-configuration/' + this.getType(),
            method: 'POST',
            params: {
                configuration: Ext.encode(this.customConfigurationPanel.getForm().getValues())
            },
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                fieldset.setLoading(false);

                if (resp.success === false) {
                    Ext.MessageBox.alert(t('error'), resp.message);
                    return;
                }

                if (resp.connector.config.hasOwnProperty('customConfiguration')) {
                    this.customConfiguration = resp.connector.config.customConfiguration;
                }

                this.afterSaveCustomConfiguration(resp);

                pimcore.helpers.showNotification(t('success'), t('social_data.connector.save_success'), 'success');

            }.bind(this),
            failure: function (response) {
                fieldset.setLoading(false);
            }
        });
    }
});