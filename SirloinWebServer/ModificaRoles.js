Ext.Loader.setPath('Ext.ux', 'extjs/examples/ux');
Ext.define('MyDesktop.ModificaRoles', {
	extend : 'Ext.ux.desktop.Module',
	requires : [ 'Ext.tab.Panel', 'Ext.chart.*', 'Ext.layout.container.Fit', 'Ext.fx.target.Sprite', 'Ext.selection.CheckboxModel' ],
	id : 'modificar-roles',
	init : function() {
		this.launcher = {
			text : 'Modificar Roles',
			id : 'launcher-modificar-roles',
			iconCls : 'tabs'
		}
	},
	createWindow : function(aplicacion, registro) {
		
		var desktop = aplicacion.getDesktop();
		var selModel = Ext.create('Ext.selection.CheckboxModel', {
			selType : 'checkboxmodel',
			showHeaderCheckbox : true,
			listeners : {
				selectionchange : function(sm, selections) {

				}
			}
		});
		var win = desktop.getWindow('modificar-roles');
		var panelModificaRoles = Ext.create('Ext.form.Panel', {
			title : '<center>Modificar Rol ' + '</center>',
			bodyStyle : 'padding:5px',
			id : 'panelModificaRoles',
			itemId : 'panelModificaRoles',
			border : false,
			// anchor : '100%',
			fieldDefaults : {
				labelAlign : 'top',
				msgTarget : 'side'
			},
			items : [ {
				layout : 'column',
				border : false,
				items : [ {
					columnWidth : 1,
					border : false,
					layout : 'anchor',
					defaultType : 'textfield',
					items : [ {
						fieldLabel : 'Nombre Rol',
						tabIndex : 3,
						value: registro.data.nombre_rol,
						allowBlank : false,
						name : 'nombre_rol',
						anchor : '100%'
					}  ]
				} ],
			} ]
		});
		
		Ext.require([ 'Ext.data.*', 'Ext.tip.QuickTipManager', 'Ext.window.MessageBox' ]);
		Ext.tip.QuickTipManager.init();
		
		if (!win) {
			win = desktop.createWindow({
				id : 'modificar-roles',
				itemId : 'modificar-roles',
				title : 'Modificar roles',
				width : 500,
				height : 150,
				modal : true,
				draggable : true,
				iconCls : 'tabs',
				animCollapse : false,
				border : false,
				constrainHeader : true,
				autoScroll : true,
				onEsc : function() {
					var me = this;
					Ext.Msg.confirm('Confirmación de cerrado', '&iquest;Est&aacute;s seguro que deseas cerrar la ventana de Modificar Roles?', function(btn) {
						if (btn == 'yes')
							me.destroy();
					});
				},
				// layout : 'fit',
				defaultType : 'container',
				items : [ panelModificaRoles, 
//					{
//					autoScroll : true,
//					itemId : 'grid-visualiza-restaurantes',
//					id : 'grid-visualiza-restaurantes',
//					xtype : 'writergrid',
//					selModel : selModel,
//					store : store
//				}
				],
				buttons : [ {
					text : 'Guardar',
					tabIndex : 20,
					handler : function(btn) {
						console.debug(this.up().up().items.get(0).getForm());
						formulario = this.up().up().items.get(0).getForm();
						panelForm = this.up().up().items.get(0);
						var s = selModel.getSelection();
						selected = [];
						Ext.each(s, function(item) {
							selected.push(item.data.id_restaurante);
						});
						var datosFormulario = [ {
							"nombre_rol" : panelForm.items.get(0).items.get(0).items.get(0).getValue(),	
							"id_rol" : registro.internalId,	
							
						} ];
						if (formulario.isValid()) {
							// console.debug("veamos la seleccion",selected);
							formulario.submit({
								waitMsg : 'Grabando en Base de Datos...',
								submitEmptyText : false,
								url : 'php/saveFormModifyRol.php',
								params : {
									store_data : Ext.encode(datosFormulario)
								},
								success : function(form, action) {
									var data = Ext.decode(action.response.responseText);
									Ext.Msg.show({
										title : 'Rol Almacenado',
										msg : 'Se guardo exitosamente el rol  <b>' + panelForm.items.get(0).items.get(0).items.get(0).getValue() + '</b> ',
										buttons : Ext.Msg.OK,
										icon : Ext.Msg.INFO
									});
									win.destroy();
									// PARA QUE CARGUE NUEVAMENTE EL GRID
									// DESPUES DE GUARDAR
									try {
										Ext.getCmp('gridRoles').store.load();
									} catch (exp) {}
								},
								failure : function(form, action) {
									//
									Ext.Msg.show({
										title : 'Problema al salvar',
										msg : 'Favor de intentar mas tarde',
										buttons : Ext.Msg.OK,
										icon : Ext.Msg.ERROR
									});
									// console.debug('entro al failure,
									// form', form, ', action', action);
								}
							});
						}
					}
				} ]
			}).show();
		}
		return win;
	}
});