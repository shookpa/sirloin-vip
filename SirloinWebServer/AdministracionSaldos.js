Ext.Loader.setPath('Ext.ux', 'extjs/examples/ux');
Ext.require([ 'Ext.grid.*', 'Ext.data.*', 'Ext.form.*', 'Ext.layout.*', 'Ext.tab.Panel', 'Ext.selection.CheckboxModel' ]);
/**
 * COMIENZO DE GRID
 */

// GUARDAMOS EN UN OBJETO LOS PERMISOS GUARDADOS EN EL sessionStorage:
var perm= JSON.parse(sessionStorage.permisos);
Ext.define('Writer.GridAdministracionSaldos', {
	extend : 'Ext.grid.Panel',
	alias : 'widget.writergridAdministracionSaldos',
	requires : [ 'Ext.form.field.Text', 'Ext.toolbar.TextItem' ],
	initComponent : function() {
		Ext.apply(this, {
			frame : false,
			multiSelect : true,
			// selType : 'checkboxmodel',
			// dockedItems: [{
			// xtype: 'toolbar',
			// items: [{
			// iconCls: 'add',
			// text: 'Agregar',
			// scope: this,
			// handler: this.onAddClick
			// }, {
			// iconCls: 'remove',
			// text: 'Eliminar',
			// disabled: true,
			// itemId: 'delete',
			// scope: this,
			// handler: this.onDeleteClick
			// }]
			// }],
			columns : [ {
				text : 'ID',
				width : 40,
				sortable : true,
				resizable : false,
				draggable : false,
				hideable : false,
				menuDisabled : true,
				dataIndex : 'id'
			}, {
				header : 'Restaurante',
				width : 90,
				sortable : true,
				dataIndex : 'nombre_restaurante',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Numero VIP',
				width : 90,
				sortable : true,
				dataIndex : 'num_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Nombre Cliente',
				width : 250,
				sortable : true,
				dataIndex : 'nom_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Telefono Cliente',
				width : 100,
				sortable : true,
				dataIndex : 'tel_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Email',
				width : 200,
				sortable : true,
				dataIndex : 'ema_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'C.P.',
				width : 80,
				sortable : true,
				dataIndex : 'cp',
				field : {
					type : 'textfield'  //fecha_nac
				}   
			}, {
				header : 'Fecha nacimiento',
				width : 120,
				sortable : true,
				dataIndex : 'fecha_nac',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Fecha compra tarjeta',
				width : 120,
				sortable : true,
				dataIndex : 'fec_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Fecha ultimo movimiento',
				width : 120,
				sortable : true,
				dataIndex : 'fuc_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Saldo tarjeta',
				width : 120,
				sortable : true,
				dataIndex : 'pto_vip',
				renderer : Ext.util.Format.usMoney,
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Tipo Movimiento',
				width : 200,
				id: "tipo_movimiento_header",
				sortable : true,
				dataIndex : 'det_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Fecha Movimiento',
				width : 120,
				id: "fecha_movimiento_header",
				sortable : true,
				dataIndex : 'fecha_mov',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'Monto Movimiento',
				width : 120,
				id: "monto_movimiento_header",
				renderer : Ext.util.Format.usMoney,
				sortable : true,
				dataIndex : 'mon_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'muc_vip',
				width : 120,
				id: "muc_vip_header",			
				sortable : true,
				dataIndex : 'muc_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'fup_vip',
				width : 120,
				id: "fup_vip_header",			
				sortable : true,
				dataIndex : 'fup_vip',
				field : {
					type : 'textfield'
				}
			}, {
				header : 'tct_vip',
				width : 120,
				id: "tct_vip_header",			
				sortable : true,
				dataIndex : 'tct_vip',
				field : {
					type : 'textfield'
				}
			} ]
		});
		this.callParent();
		this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
	},
	onSelectChange : function(selModel, selections) {
		console.debug("selections.length", selections.length);	
		if(perm!=null )
		{			
			if (perm.some(item => item.id_permiso == '4' && item.per_func1 == "1"))
				this.up().up().down('#modifyBalance').setDisabled(!(selections.length == 1));
			if (perm.some(item => item.id_permiso == '4' && item.per_func2 == "1"))
				this.up().up().down('#expiring').setDisabled(selections.length === 0);										
		}
		else
		{
			this.up().up().down('#expiring').setDisabled(selections.length === 0);
			this.up().up().down('#modifyBalance').setDisabled(!(selections.length == 1));
		}
		
	},
	onSync : function() {
		storeGridMovs3.sync();
	}
});
Ext.define('Model.Movimientos', {
	extend : 'Ext.data.Model',
	fields : [ {
		name : 'id',
		type : 'int',
		useNull : true
	}, 'mov_vip', 'num_vip', 'nom_vip', 'nombre_restaurante','id_restaurante','fecha_nac','fecha_mov', 'doc_vip', 'det_vip', 'ema_vip', 'fec_vip','fuc_vip','cp', 'tel_vip', 'fup_vip', {name:'tct_vip', type:'number'}, {name:'mon_vip', type:'number'}, {name:'muc_vip', type:'number'}, {name:'pto_vip', type:'number'}]
});
Ext.require([ 'Ext.data.*', 'Ext.tip.QuickTipManager', 'Ext.window.MessageBox' ]);
Ext.tip.QuickTipManager.init();
var storeGridMovs3 = Ext.create('Ext.data.Store', {
	model : 'Model.Movimientos',
	pageSize: 500,
	remoteSort: true ,
	autoLoad : true,
	autoSync : true,
	autoScroll : true,
	proxy : {
		type : 'ajax',
		api : {
			read : 'php/catalogos.php/movimientos/search',
		},									
		extraParams:{"restaurante":0},
		reader : {
			type : 'json',
			successProperty : 'success',
			root : 'data',
			totalProperty: 'total',
			messageProperty : 'message'
		},
		writer : {
			type : 'json',
			writeAllFields : false,
			root : 'data'
		},
		listeners : {
			exception : function(proxy, response, operation) {
				Ext.MessageBox.show({
					title : 'REMOTE EXCEPTION',
					msg : operation.getError(),
					icon : Ext.MessageBox.ERROR,
					buttons : Ext.Msg.OK
				});
			}
		}
	},
});
/**
 * FIN DE GRID
 */
Ext.define('MyDesktop.AdministracionSaldos', {
	extend : 'Ext.ux.desktop.Module',
	requires : [ 'Ext.tab.Panel', 'Ext.chart.*', 'Ext.layout.container.Fit', 'Ext.fx.target.Sprite' ],
	id : 'panel-administracion-saldos',
	init : function() {
		this.launcher = {
			text : 'Modulo Administración Saldos VIP',
			id : 'launcher-panel-administracion-saldos',
			iconCls : 'sales_report_16'
		}
	},
	createWindow : function(aplicacion, idBoletin) {
		var selModel = Ext.create('Ext.selection.CheckboxModel', {
			selType : 'checkboxmodel',
			showHeaderCheckbox : true,
			listeners : {
				selectionchange : function(sm, selections) {
				// grid4.down('#removeButton').setDisabled(selections.length === 0);
				}
			}
		});
		if(this.app!=undefined && this.app!=null)
			var app = this.app;
		else
			var app = aplicacion;
		var desktop = app.getDesktop();
		var win = desktop.getWindow('panel-administracion-saldos');		
		var idCampo=0;
		var filtros=[];
		var PanelReporteMovimientos3 = Ext.create('Ext.form.Panel', {
			title : '<center>Listado de Movimientos de Tarjetas VIP</center>',
			bodyStyle : 'padding:5px',
			itemId : 'pnlReporte3',
			id : 'PanelReporteMovimientos3',
			border : false,
			anchor : '100%',
			fieldDefaults : {
				labelAlign : 'top',
				msgTarget : 'side'
			},
// layout : 'anchor',
			items : [ {
				border : false,
// defaults : {
// anchor : '100%'
// },
// layout : 'anchor',
				dockedItems : [ {
					xtype : 'toolbar',
					items : [ {
						xtype : 'combobox',
						itemId : 'campoSeleccionado3',
						name : 'campoSeleccionado3',
						queryMode : 'local',
						typeAhead : true,						
						width: 250,
						tabIndex : 14,
						labelWidth : 50,
						listeners : {
							change : function(combo, record) {
								console.debug(combo, record);
								var panel=combo.up("#pnlReporte3");
//								if (record>0)
//									panel.down('#btnAgregarCampo2').setDisabled(false);
//								else
//									panel.down('#btnAgregarCampo2').setDisabled(true);
							}
						},
						emptyText : 'Agregue otro campo de busqueda:',
						store : Ext.create('Ext.data.Store', {
							fields : [ 'campo' ],
							itemId : 'storeCamposFiltro3',
							
							data : [ {
								"id" : 0,
								"campo" : ""
							}, {
								"id" : 1,
								"campo" : "Por restaurante"
							}, {
								"id" : 2,
								"campo" : "Por nombre cliente"
							}, {
								"id" : 3,
								"campo" : "Por numero tarjeta"
							}, {
								"id" : 4,
								"campo" : "Por saldo tarjeta"
							}, {
								"id" : 5,
								"campo" : "Por fecha apertura tarjeta"
							}, {
								"id" : 6,
								"campo" : "Por telefono"
							}, {
								"id" : 7,
								"campo" : "Por email"
							}, {
								"id" : 8,
								"campo" : "Por Codigo Postal"
							}, {
								"id" : 9,
								"campo" : "Por fecha de nacimiento"
							}, {
								"id" : 10,
								"campo" : "Por mes de nacimiento"
							}, {
								"id" : 11,
								"campo" : "Por tipo de movimiento"
							}, {
								"id" : 12,
								"campo" : "Por monto de movimiento"
							}, {
								"id" : 13,
								"campo" : "Por fecha de movimiento"
							}, {
								"id" : 14,
								"campo" : "Por Uso de la tarjeta"
							}  ]
						}),
						displayField : 'campo',
						valueField : 'id',
						forceSelection : false
					},{
						iconCls : 'add',
						text : 'Agregar',
//						disabled: true,
						itemId : 'btnAgregarCampo2',
						scope : this,
						handler : function(btn) {
								idCampo++;
								// en este momento tenemos que agregar el
								// elemento conforme sea necesario:
								// y tambien eliminar la opción del combo actual
								// de seleccion del campo
								console.debug("Por CREAR el campo:"+idCampo);
								var panel=btn.up("#pnlReporte3");
								var toolbar = panel.down('#toolbarCampos3');
								var campoSel=panel.down('#campoSeleccionado3').getValue();
								console.debug("Veamos el campo seleccionado:",campoSel);
								switch(campoSel) {
								    case 1:// Por restaurante
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'restaurante',
											name : 'restaurante',
											fieldLabel: 'Por restaurante',
											queryMode : 'local',
											typeAhead : true,
											tabIndex : 13,
											labelWidth : 50,
											width: 190,
											emptyText : 'Seleccione un restaurante',
											displayField : 'nombre_restaurante',
											valueField : 'id_restaurante',
											forceSelection : true,
											store : app.storeRestaurantes
										});	

								    	
								    break;
								    case 2:	// Por nombre cliente
								    	toolbar.add({
								    		xtype : 'textfield',
											name : 'nombreCliente',
											fieldLabel: 'Por nombre cliente',
											width: 180,
											itemId : 'nombreCliente',
											emptyText : 'Ingrese el nombre del Cliente'
										});	
								    break;
								    case 3:	// Por numero tarjeta
								    	toolbar.add({
								    		xtype : 'textfield',
											name : 'tarjeta',
											fieldLabel: 'Por numero tarjeta',
											width: 170,
											itemId : 'tarjeta',
											emptyText : 'Ingrese el número de tarjeta'
										});	
								    break;
								    case 4:	// Por saldo tarjeta
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'operSaldo1',
											fieldLabel: ' ',
											name : 'operSaldo1',
											value:">=",
											queryMode : 'local',
											typeAhead : true,
											width: 40,
											tabIndex : 14,
											labelWidth : 50,
											forceSelection : true,
											store : Ext.create('Ext.data.Store', {
												fields : [ 'op' ],
												data : [  {													
													"op" : "="
												}, {													
													"op" : ">"
												}, {													
													"op" : "<"
												}, {													
													"op" : "<="
												}, {													
													"op" : ">="
												}, {													
													"op" : "<>"
												} ]
											}),
											displayField : 'op',
											valueField : 'op'
											
										});
								    	toolbar.add({
											xtype : 'textfield',
											name : 'saldoMin',
											fieldLabel: 'Por saldo tarjeta',
											itemId : 'saldoMin',
											width: 150,
											// allowBlank : false,
											maskRe : /[0-9\.]/,
											renderer : Ext.util.Format.numberRenderer('$ 000,000.'),
											enableKeyEvents : true,
											emptyText : 'Ingrese saldo minimo',
										// fieldLabel: 'Comienzo Movimientos:'
										});
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'operSaldo2',
											name : 'operSaldo2',
											queryMode : 'local',
											value:"<=",
											fieldLabel: ' ',
											typeAhead : true,
											width: 40,
											tabIndex : 14,
											labelWidth : 50,
											forceSelection : true,
											store : Ext.create('Ext.data.Store', {
												fields : [ 'op' ],
												data : [  {													
													"op" : "="
												}, {													
													"op" : ">"
												}, {													
													"op" : "<"
												}, {													
													"op" : "<="
												}, {													
													"op" : ">="
												}, {													
													"op" : "<>"
												} ]
											}),
											displayField : 'op',
											valueField : 'op'
											
										});
									    	toolbar.add({
											xtype : 'textfield',
											name : 'saldoMax',
											width: 150,
											fieldLabel: ' ',
											itemId : 'saldoMax',
											// allowBlank : false,
											emptyText : 'Ingrese saldo maximo',
											maskRe : /[0-9\.]/,
											renderer : Ext.util.Format.numberRenderer('$ 000,000.'),
											enableKeyEvents : true,
										});
								    break;
								    case 5:	// Por fecha apertura tarjeta
								    	toolbar.add( {
										xtype : 'datefield',
										fieldLabel: 'Por fecha apertura',
										name : 'fechaApertura',
										itemId : 'fechaApertura',
										width: 240,
										emptyText : 'Seleccione fecha de apertura de tarjeta',
										format : 'Y-m-d',
									});
									break;
								    case 6:	// Por telefono
								    	toolbar.add({
								    		xtype : 'textfield',
											name : 'telefono',
											fieldLabel: 'Por telefono',
											width: 170,
											itemId : 'telefono',
											emptyText : 'Ingrese el telefono'
										});	
									break;
								    case 7:	// Por email
								    	toolbar.add({
								    		xtype : 'textfield',
											name : 'email',
											vtype : 'email',
											fieldLabel: 'Por email',
											width: 170,
											itemId : 'email',
											emptyText : 'Ingrese el email'
										});	
									break;
								    case 8:	// Por Codigo Postal
								    	toolbar.add({
								    		xtype : 'textfield',
											name : 'cp',
											fieldLabel: 'Por codigo postal',
											width: 170,
											itemId : 'cp',
											emptyText : 'Ingrese el Codigo Postal'
										});	
									break;
								    case 9:	// Por fecha de nacimiento
								    	toolbar.add({
											xtype : 'datefield',
											name : 'fechaNacIni',
											fieldLabel: 'Por fecha de nacimiento',
											itemId : 'fechaNacIni',
											width: 240,
											emptyText : 'Seleccione fecha de nacimiento inicial',
											format : 'Y-m-d',
									    	});
									    	toolbar.add({
											xtype : 'datefield',
											name : 'fechaNacFin',
											fieldLabel: ' ',
											itemId : 'fechaNacFin',
											width: 240,
											emptyText : 'Seleccione fecha de nacimiento final',
											format : 'Y-m-d',
									    	});
								    break;	
								    case 10:	// Por mes de nacimiento
								    	toolbar.add({
								    		xtype : 'combobox',
											name : 'mesNac',
											fieldLabel: 'Por mes de nacimiento',
											itemId : 'mesNac',
											displayField : 'mes',
											valueField : 'id',
											forceSelection : true,
											width: 240,
											emptyText : 'Seleccione mes de nacimiento',
											store : Ext.create('Ext.data.Store', {
												fields : [ 'mes' ],
												data : [ {
													"id" : 1,
													"mes" : "Enero"
												}, {
													"id" : 2,
													"mes" : "Febrero"
												}, {
													"id" : 3,
													"mes" : "Marzo"
												}, {
													"id" : 4,
													"mes" : "Abril"
												}, {
													"id" : 5,
													"mes" : "Mayo"
												}, {
													"id" : 6,
													"mes" : "Junio"
												}, {
													"id" : 7,
													"mes" : "Julio"
												}, {
													"id" : 8,
													"mes" : "Agosto"
												}, {
													"id" : 9,
													"mes" : "Septiembre"
												}, {
													"id" : 10,
													"mes" : "Octubre"
												}, {
													"id" : 11,
													"mes" : "Noviembre"
												}, {
													"id" : 12,
													"mes" : "Diciembre"
												} ]
											}),
									    	});
									    	
								    break;	
								    case 11:// Por tipo de movimiento
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'tipo_mov',
											name : 'tipo_mov',
											fieldLabel: 'Por tipo de movimiento',
											queryMode : 'local',
											typeAhead : true,
											width: 220,
											tabIndex : 14,
											labelWidth : 50,
											emptyText : 'Seleccione el tipo de movimiento',
											store : Ext.create('Ext.data.Store', {
												fields : [ 'movimiento' ],
												data : [ {
													"id" : 0,
													"movimiento" : "Todos los movimientos"
												}, {
													"id" : 1,
													"movimiento" : "Saldo utilizado (por compra del cliente)"
												}, {
													"id" : 2,
													"movimiento" : "Carga de saldo (por venta al cliente)"
												}, {
													"id" : 3,
													"movimiento" : "Activacion de tarjeta en sistema web"
												}, {
													"id" : 4,
													"movimiento" : "Registro de tarjeta en sistema web"
												}, {
													"id" : 5,
													"movimiento" : "Modificacion de saldo desde Sistema Web"
												}, {
													"id" : 6,
													"movimiento" : "Vencimiento de puntos"
												} ]
											}),
											displayField : 'movimiento',
											valueField : 'id',
											forceSelection : true
										});
									break;
								    case 12:// Por monto de movimiento
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'operMov1',
											name : 'operMov1',
											fieldLabel: ' ',
											queryMode : 'local',
											typeAhead : true,
											width: 40,
											value:">=",
											tabIndex : 14,
											labelWidth : 50,
											forceSelection : true,
											store : Ext.create('Ext.data.Store', {
												fields : [ 'op' ],
												data : [  {													
													"op" : "="
												}, {													
													"op" : ">"
												}, {													
													"op" : "<"
												}, {													
													"op" : "<="
												}, {													
													"op" : ">="
												}, {													
													"op" : "<>"
												} ]
											}),
											displayField : 'op',
											valueField : 'op'
											
										});
								    	toolbar.add({
										xtype : 'textfield',
										name : 'montoMin',
										fieldLabel: 'Por monto de movimiento',
										itemId : 'montoMin',
										width: 150,
										maskRe : /[0-9\.]/,
										renderer : Ext.util.Format.numberRenderer('$ 000,000.'),
										enableKeyEvents : true,
										emptyText : 'Ingrese monto minimo',
									// fieldLabel: 'Comienzo Movimientos:'
									});
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'operMov2',
											fieldLabel: ' ',
											name : 'operMov2',
											queryMode : 'local',
											typeAhead : true,
											width: 40,
											value:"<=",
											tabIndex : 14,
											labelWidth : 50,
											forceSelection : true,
											store : Ext.create('Ext.data.Store', {
												fields : [ 'op' ],
												data : [  {													
													"op" : "="
												}, {													
													"op" : ">"
												}, {													
													"op" : "<"
												}, {													
													"op" : "<="
												}, {													
													"op" : ">="
												}, {													
													"op" : "<>"
												} ]
											}),
											displayField : 'op',
											valueField : 'op'
										});
								    	toolbar.add({
										xtype : 'textfield',
										fieldLabel: ' ',
										name : 'montoMax',
										width: 150,
										itemId : 'montoMax',
										emptyText : 'Ingrese monto maximo',
										maskRe : /[0-9\.]/,
										renderer : Ext.util.Format.numberRenderer('$ 000,000.'),
										enableKeyEvents : true,
									});
									break;
								    case 13:// Por fecha de movimiento
								    	toolbar.add({
										xtype : 'datefield',
										name : 'fechaMovIni',
										fieldLabel: 'Por fecha de movimiento',
										itemId : 'fechaMovIni',
										width: 240,
										emptyText : 'Seleccione fecha de movimiento inicial',
										format : 'Y-m-d',
								    	});
								    	toolbar.add({
										xtype : 'datefield',
										name : 'fechaMovFin',
										fieldLabel: ' ',
										itemId : 'fechaMovFin',
										width: 240,
										emptyText : 'Seleccione fecha de movimiento final',
										format : 'Y-m-d',
								    	});
									break;
									
									
								    case 14:// Por uso de la tarjeta
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'operUso',
											name : 'operUso',
											queryMode : 'local',
											fieldLabel: ' ',
											typeAhead : true,
											width: 40,
											value:"=",
											tabIndex : 14,
											labelWidth : 50,
											forceSelection : true,
											store : Ext.create('Ext.data.Store', {
												fields : [ 'op' ],
												data : [  {													
													"op" : "="
												}, {													
													"op" : ">"
												}, {													
													"op" : "<"
												}, {													
													"op" : "<="
												}, {													
													"op" : ">="
												}, {													
													"op" : "<>"
												} ]
											}),
											displayField : 'op',
											valueField : 'op'
										});
								    	toolbar.add({
											xtype : 'textfield',
											fieldLabel: 'Por uso de la tarjeta',
											name : 'numeroVeces',
											width: 150,
											itemId : 'numeroVeces',
											// allowBlank : false,
											emptyText : '# veces utilizadas',
											maskRe : /[0-9\.]/,
											enableKeyEvents : true,
										});
								    	toolbar.add({
										xtype : 'datefield',
										name : 'fechaUsoIni',
										fieldLabel: ' ',
										itemId : 'fechaUsoIni',
										width: 240,
										emptyText : 'Seleccione fecha de movimiento inicial',
										format : 'Y-m-d',
								    	});
								    	toolbar.add({
										xtype : 'datefield',
										name : 'fechaUsoFin',
										fieldLabel: ' ',
										itemId : 'fechaUsoFin',
										width: 240,
										emptyText : 'Seleccione fecha de movimiento final',
										format : 'Y-m-d',
								    	});
								    	toolbar.add({
											xtype : 'combobox',
											itemId : 'periodo',
											name : 'periodo',
											fieldLabel: ' ',
											queryMode : 'local',
											typeAhead : true,
											width: 150,
											value:0,
											tabIndex : 14,
											labelWidth : 50,
											emptyText : 'Seleccione el periodo',
											store : Ext.create('Ext.data.Store', {
												fields : [ 'peri' ],
												data : [ {
													"id" : 0,
													"peri" : "En ese rango"
												}, {
													"id" : 1,
													"peri" : "En un día"
												}, {
													"id" : 2,
													"peri" : "En un mes"
												} ]
											}),
											displayField : 'peri',
											valueField : 'id',
											forceSelection : true
										});
									break;
								    default:
								        
								    break;
								}
								var storeItems = panel.down('#campoSeleccionado3').getStore().data.items;
						    	var storeData = panel.down('#campoSeleccionado3').getStore();
//						    	Si se agregan filtros de busqueda, el valor de abjo debe corresponder:
//						    	campoSel= campoSel - (14 - storeData.data.length);
//						    	storeItems.forEach( function(item,index){
//						    		
//							    	if(item.data.id==campoSel){
//							    		storeData.removeAt(campoSel);							    		
//							    		panel.down('#campoSeleccionado2').setValue(0);
//							    	}
//						    	});
						    	storeData.removeAt(storeData.indexOfId(campoSel));
						    	panel.down('#campoSeleccionado3').setValue(0);
								storeData.sort("id", "ASC");
								
														
							
						}
						
					},{
						iconCls : 'remove',
						text : 'Limpiar campos de búsqueda',
						disabled: perm!=null && (!perm.some(item => item.id_permiso == '3' && item.per_func2 == "1") && perm.some(item => item.id_permiso == '3' && item.per_func1 == "1")),
						itemId : 'btnEliminarCampo',
						scope : this,
						handler : function(btn) {
								idCampo--;	
								console.debug("Por ELIMINAR el campo:"+idCampo);
								var panel=btn.up("#pnlReporte3");
								// Eliminar el ultimo campo agregado al toolbar
								// y tambien agregar la opción al combo
								var toolbar = panel.down('#toolbarCampos3'); 
								
								// verificamos que campo es para agregarlo al
								// combo:
								var storeData = panel.down('#campoSeleccionado3').getStore();
								storeData.removeAll();
								storeData.add({"id" : 0,"campo" : ""});
								storeData.add({"id" : 1,"campo" : "Por restaurante"});
								storeData.add({"id" : 2,"campo" : "Por nombre cliente"});
								storeData.add({"id" : 3,"campo" : "Por numero tarjeta"});
								storeData.add({"id" : 4,"campo" : "Por saldo tarjeta"});
								storeData.add({"id" : 5,"campo" : "Por fecha apertura tarjeta"});
								storeData.add({"id" : 6,"campo" : "Por telefono"});
								storeData.add({"id" : 7,"campo" : "Por email"});
								storeData.add({"id" : 8,"campo" : "Por Codigo Postal"});								
								storeData.add({"id" : 9,"campo" : "Por fecha de nacimiento"});
								storeData.add({"id" : 10,"campo" : "Por mes de nacimiento"});
								storeData.add({"id" : 11,"campo" : "Por tipo de movimiento"});
								storeData.add({"id" : 12,"campo" : "Por monto de movimiento"});
								storeData.add({"id" : 13,"campo" : "Por fecha de movimiento"});
								storeData.add({"id" : 14,"campo" : "Por Uso de la tarjeta"});
								panel.down('#campoSeleccionado3').setValue(0);
					
								storeData.sort("id", "ASC");

								toolbar.removeAll();
																
								storeGridMovs3.setProxy({
									type : 'ajax',
									 actionMethods: {
								          read: 'POST'
								      },
									api : {
										read : 'php/catalogos.php/movimientos/search',
									},									
									filtros:{"id_restaurante":0},
									reader : {
										type : 'json',
										successProperty : 'success',
										totalProperty: 'total',
										root : 'data',
										messageProperty : 'message'
									},
									writer : {
										type : 'json',
										writeAllFields : false,
										root : 'data'
									},
									listeners : {
										exception : function(proxy, response, operation) {
											Ext.MessageBox.show({
												title : 'REMOTE EXCEPTION',
												msg : operation.getError(),
												icon : Ext.MessageBox.ERROR,
												buttons : Ext.Msg.OK
											});
										}
									}
								});
								storeGridMovs3.load();
							
						}
						
					},
					{
						iconCls : 'icon-find1',
						text : 'Buscar',
						disabled: perm!=null && (!perm.some(item => item.id_permiso == '3' && item.per_func2 == "1") && perm.some(item => item.id_permiso == '3' && item.per_func1 == "1")),
						itemId : 'btnBuscar',
						scope : this,
						listeners : {
							click : function(textfield, eventObjet) {
								var panel=textfield.up("#pnlReporte3");
								var toolbar = panel.down('#toolbarCampos3'); 
								filtros=[];
								for (var i = 0; i < toolbar.items.items.length; i++) {
									console.debug("Toolbar items:",toolbar.items.items[i].itemId,toolbar.items.items[i].value);
									filtros[toolbar.items.items[i].itemId]=toolbar.items.items[i].value;
								}
								
								
								if (toolbar.items.items.length>0)
								{
									//que primero que limpie la busqueda que tien actualmente
									if(storeGridMovs3.currentPage>1)
										storeGridMovs3.loadPage(1); 
//									storeGridMovs3.load();
									//y despues que vuelva a buscar
									storeGridMovs3.setProxy({
										type : 'ajax',
										 actionMethods: {
									          read: 'POST'
									      },
										api : {
											read : 'php/catalogos.php/movimientos/search',
										},
										// de esta forma le vamos agregando los
										// parametros en lugar de la url
										extraParams: 
											filtros
					                    ,
										reader : {
											type : 'json',
											successProperty : 'success',
											totalProperty: 'total',
											root : 'data',
											messageProperty : 'message'
										},
										writer : {
											type : 'json',
											writeAllFields : false,
											root : 'data'
										},
										listeners : {
											exception : function(proxy, response, operation) {
												Ext.MessageBox.show({
													title : 'REMOTE EXCEPTION',
													msg : operation.getError(),
													icon : Ext.MessageBox.ERROR,
													buttons : Ext.Msg.OK
												});
											}
										}
									});
								}
									storeGridMovs3.load();
								
							}
						}
					//							                            
					} ]
				}, {
					xtype : 'toolbar',
					itemId : 'toolbarCampos3',
					  enableOverflow: true,
					  dock: 'top',
					
				} ],
				items : [ {
					itemId : 'gridMovimientos3',
					id : 'gridMovimientos3',
					xtype : 'writergridAdministracionSaldos',
					selModel : selModel,
					height : 400,
					store : storeGridMovs3,
					 dockedItems: [{
					        xtype: 'pagingtoolbar',
					        store: storeGridMovs3,   // same store GridPanel is
													// using
					        dock: 'bottom',
					        displayInfo: true
					    }]
				} ],
				buttons : [  {
					text : 'Modificar Puntos',
					tabIndex : 21,
					disabled:true,
					itemId : 'modifyBalance',
					handler : function(btn) {
						var selection = selModel.getSelection()[0];
						modulo = new MyDesktop.ModificarSaldo();
						console.debug("veamos la seleccion",selection.data);
						var window = modulo.createWindow(app, selection.data);
						window.show();
					}
				},{
					text : 'Vencimiento Puntos',
					tabIndex : 21,
					disabled: perm!=null && !perm.some(item => item.id_permiso == '4' && item.per_func1 == "1"),
					method      : 'POST',
					disabled:true,
					itemId : 'expiring',
					params:{ xtype: 'pagingtoolbar'},
					baseParams:{ dock: 'bottom'},
					handler : function(btn) {
						var s = selModel.getSelection();
						// And then you can iterate over the selected items,
						// e.g.:
						selected = [];
						Ext.each(s, function(item) {
							if(item.data.ema_vip!=null && item.data.ema_vip!="" && !selected.includes(item.data.ema_vip))
								selected.push(item.data.num_vip);
						});
						console.debug(selected);
						var datosEnviar= [{
	                    	 "tarjetas" :  				[{selected}]	                    	 
				    	}];
						console.debug("Veamos los datos a enviar: ",datosEnviar);
						Ext.Ajax.request({
							url : 'php/expiration.php',
							method : 'POST',
							params : {
								"store_data" :  Ext.encode(datosEnviar)
							},
							success : function(response) {
								var data = Ext.decode(response.responseText);
	                        	Ext.Msg.alert('Vencimiento de puntos Exitoso', data.mensaje);	
	                        	Ext.getCmp('gridMovimientos3').store.load();
							},
							failure : function(response) {
								console.log(response.responseText);
							}
						});
						

					}
				}]
			} ]
		});
		
		if (!win) {
			win = desktop.createWindow({
				id : 'panel-administracion-saldos', 
				title : 'Modulo de Administración Saldos',
				width :  1500,
				height :600,
				modal : true,
				draggable : true,
				iconCls : 'sales_report_16',
				animCollapse : false,
				border : false,
				constrainHeader : true,
				autoScroll : true,
				onEsc : function() {
					var me = this;
					Ext.Msg.confirm('Confirmación de cerrado', '&iquest;Est&aacute;s seguro que deseas cerrar el Panel?', function(btn) {
						if (btn == 'yes')
							me.destroy();
					});
				},
				layout : 'fit',
				defaultType : 'container',
				items : [ {
					xtype : 'tabpanel',
					activeTab : 0,
					bodyStyle : 'padding: 5px;',
					items : [ {
						title : 'Listado de Tarjetas VIP',
						header : false,
						border : false,
						items : [ PanelReporteMovimientos3 ]
					} ]
				} ]
			}).show();
		}

		PanelReporteMovimientos3.down("#btnEliminarCampo").fireHandler("click");
		return win;
		
	}
});