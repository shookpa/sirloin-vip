Ext.Loader.setPath('Ext.ux', 'extjs/examples/ux');
Ext.define('MyDesktop.CreaUsuarios', {
	extend : 'Ext.ux.desktop.Module',
	requires : [ 'Ext.tab.Panel', 'Ext.chart.*', 'Ext.layout.container.Fit', 'Ext.fx.target.Sprite' ,'Ext.selection.CheckboxModel'],
	id : 'crear-usuarios',
	init : function() {
		this.launcher = {
			text : 'Crear Usuarios',
			id : 'launcher-crear-usuarios',
			iconCls : 'tabs'
		}
	},
	createWindow : function(aplicacion, registro) {
		// Ext.suspendLayouts();
		// var desktop =this.app.getDesktop();
		// console.debug("Ya entro donde queria createWindow");
		var desktop = aplicacion.getDesktop();
		var selModel = Ext.create('Ext.selection.CheckboxModel', {
			selType : 'checkboxmodel',
			showHeaderCheckbox : true,
			listeners : {
				selectionchange : function(sm, selections) {
				// grid4.down('#removeButton').setDisabled(selections.length === 0);
				}
			}
		});
		
		Ext.define('ModelRoles', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_rol', type:'int'},              
                {name:'nombre_rol', type:'string'}
            ]
        });
		var storeRoles = Ext.create('Ext.data.Store', {
        	model: 'ModelRoles',    
	        proxy: {	            
	            type: 'ajax',
	            url: 'php/cargaCombos.php',		            
	            reader: {
	                root: 'datos'
	            }
	        }
        });
        
        storeRoles.load({
            params: {
            	tabla: 'cat_roles',
            	filtros: 'WHERE status=1'
            }
        });
		Ext.define('ModelEmpresa', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_empresa', type:'int'},              
                {name:'nombre_empresa', type:'string'}
            ]
        });
		var storeEmpresa = Ext.create('Ext.data.Store', {
        	model: 'ModelEmpresa',    
	        proxy: {	            
	            type: 'ajax',
	            url: 'php/cargaCombos.php',		            
	            reader: {
	                root: 'datos'
	            }
	        }
        });
		storeEmpresa.load({
            params: {
            	tabla: 'cat_empresas'
            	
            }
        }); 
		Ext.define('Writer.GridCreaUsuarios', {
			extend : 'Ext.grid.Panel',
			alias : 'widget.writergridCreaUsuarios',
			requires : [

			'Ext.form.field.Text', 'Ext.toolbar.TextItem' ],
			initComponent : function() {
				Ext.apply(this, {
					xtype : 'array-grid',
					stateful : true,

					multiSelect : true,
					stateId : 'stateGrid',

					viewConfig : {
						stripeRows : true,
						enableTextSelection : true
					},

					columns : [

					{
						header : 'ID',

						width : 50,
						dataIndex : 'id_restaurante'

					}, {
						header : 'Nombre Restaurante',
						width : 170,

						dataIndex : 'nombre_restaurante'

					} ]
				});
				this.callParent();
				this.getSelectionModel().on('selectionchange',
						this.onSelectChange, this);
			},
			onSelectChange : function(selModel, selections) {
				// this.down('#delete').setDisabled(selections.length
				// === 0);
				// this.down('#pdf').setDisabled(selections.length
				// === 0);
				// this.down('#enviar').setDisabled(selections.length
				// === 0);
				// this.down('#update').setDisabled(selections.length
				// === 0);
				// this.down('#createSolicitud').setDisabled(selections.length
				// === 0);

			},
			onSync : function() {
				this.store.sync();
			},

		});
		Ext.define('Writer.Restaurant', {
			extend : 'Ext.data.Model',
			fields : [ 'id_restaurante','nombre_restaurante']

		});
		Ext.require([ 'Ext.data.*', 'Ext.tip.QuickTipManager',
				'Ext.window.MessageBox' ]);

		Ext.tip.QuickTipManager.init();
		var store = Ext
				.create(
						'Ext.data.Store',
						{
							model : 'Writer.Restaurant',
							autoLoad : true,

							sorters : [ {
								property : 'id',
								direction : 'DESC'
							} ],
							autoSync : true,
							autoScroll : true,

							// listeners :
							// {
							// bulkremove :
							// function(store,records, indexes,
							// isMove, eOpts) {
							// console.debug("Ya estamos en el
							// remove event bulkremove 2");
							// task.delay(20000);
							// Ext.getCmp('grid-registro-ingresos-egresos').store.load();
							//														
							// }
							//												
							// },

							proxy : {
								type : 'ajax',
								api : {
									read : 'php/catalogos.php/restaurantes/view'
									
								},
								reader : {
									type : 'json',
									successProperty : 'success',
									root : 'data',
									messageProperty : 'message'
								},
								writer : {
									type : 'json',
									writeAllFields : false,
									root : 'data'
								},
								listeners : {
									exception : function(proxy,
											response, operation) {
										Ext.MessageBox
												.show({
													title : 'REMOTE EXCEPTION',
													msg : operation
															.getError(),
													icon : Ext.MessageBox.ERROR,
													buttons : Ext.Msg.OK
												});
									}

								}
							}

						});
		var win = desktop.getWindow('crear-usuarios');
		var panelCreaUsuarios = Ext.create('Ext.form.Panel', {
			title : '<center>Crear Usuario ' + '</center>',
			bodyStyle : 'padding:5px',
			id : 'panelCreaUsuarios',
			itemId : 'panelCreaUsuarios',
			border : false,
//			anchor : '100%',
			fieldDefaults : {
				labelAlign : 'top',
				msgTarget : 'side'
			},
//			defaults : {
//				anchor : '100%'
//			},
			items : [ {
				layout : 'column',
				border : false,
				items : [ {
					columnWidth : .5,
					border : false,
					layout : 'anchor',
					defaultType : 'textfield',
					items : [ {
						fieldLabel : 'Tipo Usuario',
						xtype : 'combo',
						name : 'tipoUser',
						tabIndex : 1,
						allowBlank : false,
						itemId : 'crear-usuarios-tipo-usuario',
						// id: 'tituloCot',
						anchor : '100%',
						emptyText : 'Selecciona...',
						displayField : 'descripcion',
						queryMode : 'local',
						typeAhead : true,
						valueField : 'id',
						forceSelection : true,
						store : aplicacion.storeTiposUsuario,
						listeners:{
							select: function (combo, record){					
								 	
								if(this.getValue()==1 || this.getValue()=='1')
								{
									panelCreaUsuarios.items.get(0).items.get(0).items.get(1).allowBlank=true;
									panelCreaUsuarios.items.get(0).items.get(1).items.get(0).allowBlank=true;
									panelCreaUsuarios.items.get(0).items.get(0).items.get(1).setVisible(false);
									panelCreaUsuarios.items.get(0).items.get(1).items.get(0).setVisible(false);
								}
									
								else
								{
									panelCreaUsuarios.items.get(0).items.get(0).items.get(1).allowBlank=false;
									panelCreaUsuarios.items.get(0).items.get(1).items.get(0).allowBlank=false;
									panelCreaUsuarios.items.get(0).items.get(0).items.get(1).setVisible(true);
									panelCreaUsuarios.items.get(0).items.get(1).items.get(0).setVisible(true);
								}
								
								
							}
						}
					},{
						fieldLabel : 'Rol Usuario',
						xtype : 'combo',
						name : 'rol_user',
						tabIndex : 4,
						allowBlank : false,
						// id: 'tituloCot',
						anchor : '100%',
						emptyText : 'Selecciona...',
						displayField : 'nombre_rol',
						queryMode : 'local',
						typeAhead : true,
						valueField : 'id_rol',
						forceSelection : true,
						store : storeRoles,
						listeners:{
							select: function (combo, record){
//								if(this.getValue()==1 || this.getValue()=='1')
//									Ext.getCmp('grid-visualiza-restaurantes').hide();
//								else
//									Ext.getCmp('grid-visualiza-restaurantes').show();
								
							}
						}
					},  {
						fieldLabel : 'Nombre',
						tabIndex : 6,
						allowBlank : false,
						name : 'nombre',
						anchor : '100%'
					},  {
						fieldLabel : 'Apellido Materno',
						name : 'a_materno',
						
						allowBlank : false,
						tabIndex : 9,
						anchor : '100%'
					},{
						fieldLabel : 'Telefono',
						name : 'telefono',
						allowBlank : false,
						// queryMode: 'local',
						// maskRe: /[RS0-9]/,
						tabIndex : 13,
						anchor : '100%',
					// matchFieldWidth: false
					} ]
				}, {
					columnWidth : .5,
					border : false,
					layout : 'anchor',
					defaultType : 'textfield',
					items : [{
						fieldLabel : 'Empresa',
						xtype : 'combo',
						name : 'empresa',
						tabIndex : 3,
						allowBlank : false,
						// id: 'tituloCot',
						anchor : '100%',
						emptyText : 'Selecciona...',
						displayField : 'nombre_empresa',
						queryMode : 'local',
						typeAhead : true,
						valueField : 'id_empresa',
						forceSelection : true,
						store : storeEmpresa,
						listeners:{
							select: function (combo, record){
								
//								store.load({
//							            params: {
//							            	tabla: 'cat_tipos_usuario',
//							            	filtros: 'INNER JOIN WHERE id IN (1,3) '
//							            }
//							        });  
								store.setProxy({
									type : 'ajax',
									api : {
										read : 'php/catalogos.php/restaurantes/view/'+ this.getValue()
										
									},
									reader : {
										type : 'json',
										successProperty : 'success',
										root : 'data',
										messageProperty : 'message'
									},
									writer : {
										type : 'json',
										writeAllFields : false,
										root : 'data'
									},
									listeners : {
										exception : function(proxy,
												response, operation) {
											Ext.MessageBox
													.show({
														title : 'REMOTE EXCEPTION',
														msg : operation
																.getError(),
														icon : Ext.MessageBox.ERROR,
														buttons : Ext.Msg.OK
													});
										}

									}
								});
								store.load();

									Ext.getCmp('grid-visualiza-restaurantes').show();
								
							}
						}
					},{
						fieldLabel : 'Apellido Paterno',
						tabIndex : 5,
						allowBlank : false,
						name : 'a_paterno',
						anchor : '100%'
					},{
						fieldLabel : 'Email',
						tabIndex : 11,
						vtype : 'email',
						 allowBlank : false,
						name : 'email',
						anchor : '100%'
					},{
						fieldLabel : 'Password',
						name : 'password',
						inputType	: 'password',
						allowBlank : false,
						// queryMode: 'local',
						// maskRe: /[RS0-9]/,
						tabIndex : 15,
						anchor : '100%',
					// matchFieldWidth: false
					} ],
					
				} ]
			} ]
		});
		
		if (!win) {
			win = desktop.createWindow({
				id : 'crear-usuarios',
				itemId : 'crear-usuarios',
				title : 'Crear Usuarios',
				width : 550,
				height : 450,
				modal : true,
				draggable : true,
				iconCls : 'tabs',
				animCollapse : false,
				border : false,
				constrainHeader : true,
				autoScroll : true,
				onEsc : function() {
					var me = this;
					Ext.Msg.confirm('Confirmación de cerrado', '&iquest;Est&aacute;s seguro que deseas cerrar la ventana de Crear Usuarios?', function(btn) {
						if (btn == 'yes')
							me.destroy();
					});
				},
				//layout : 'fit',
				defaultType : 'container',
				items : [ panelCreaUsuarios, {
					autoScroll : true,
					itemId : 'grid-visualiza-restaurantes',
					id : 'grid-visualiza-restaurantes',
					hidden:true,
					visible:false,
					xtype : 'writergridCreaUsuarios',
					selModel : selModel,
					store : store
				}],
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
							"tipoUser" : 	panelForm.items.get(0).items.get(0).items.get(0).getValue(),
							"rol_user" : 	panelForm.items.get(0).items.get(0).items.get(1).getValue(),
							"id_empresa" : 	panelForm.items.get(0).items.get(1).items.get(0).getValue(),
							"nombre" : 		panelForm.items.get(0).items.get(0).items.get(2).getValue(), 
							"a_paterno" :   panelForm.items.get(0).items.get(1).items.get(1).getValue(),
							"a_materno" : 	panelForm.items.get(0).items.get(0).items.get(3).getValue(),
							"email" : 		panelForm.items.get(0).items.get(1).items.get(2).getValue(),							
							"telefono" : 	panelForm.items.get(0).items.get(0).items.get(4).getValue(),
							"password" : 	panelForm.items.get(0).items.get(1).items.get(3).getValue(),
							"restaurantes": selected
						} ];
						if (formulario.isValid()) {							
							
//							console.debug("veamos la seleccion",selected);
							formulario.submit({
								waitMsg : 'Grabando en Base de Datos...',
								submitEmptyText : false,
								url : 'php/saveFormCreateUser.php',
								params : {
									store_data : Ext.encode(datosFormulario)
								},
								success : function(form, action) {
									var data = Ext.decode(action.response.responseText);
									Ext.Msg.show({
										title : 'Usuario Almacenado',
										msg : 'Se guardo exitosamente el usuario  <b>' + panelForm.items.get(0).items.get(0).items.get(2).getValue()+ '</b> ',
										buttons : Ext.Msg.OK,
										icon : Ext.Msg.INFO
									});
									win.destroy();
									// PARA QUE CARGUE NUEVAMENTE EL GRID
									// DESPUES DE GUARDAR
									try {
										Ext.getCmp('gridUsuarios').store.load();
									} catch (exp) {}
								},
								failure : function(form, action) {
									var data = Ext.decode(action.response.responseText);
									Ext.Msg.show({
										title : 'Problema al salvar',
										msg : data.mensaje,
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

		var tip = Ext.create('Ext.tip.ToolTip', {
		    target: panelCreaUsuarios.items.get(0).items.get(0).items.get(0).id,
		    html: 'El tipo de usuario permite seleccionar si es un administrador <br> con todos los permisos, o un administrador con ciertos privilegios'
		});
		var tip2 = Ext.create('Ext.tip.ToolTip', {
		    target: panelCreaUsuarios.items.get(0).items.get(0).items.get(1).id,
		    html: 'Dependiendo del rol seleccionado se le asignaran <br> los permisos de acceso en los modulos del sistema'
		});
		
		return win;
	}
});