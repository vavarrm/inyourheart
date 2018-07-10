'use strict';
if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
	var user_terminal = "cellPhone";
} else if (/(Android)/i.test(navigator.userAgent)) {
	var user_terminal = "cellPhone";
} else {
    var user_terminal = "pc";
};
// user_terminal = "cellPhone";
var adminApp = angular.module("adminApp", ['ngRoute', 'ngCookies']);
var CURRENT_URL,
	$BODY,
    $MENU_TOGGLE,
	$SIDEBAR_MENU,
	$SIDEBAR_FOOTER,
	$LEFT_COL,
	$RIGHT_COL,
	$NAV_MENU,
	$FOOTER,
	randNum ;
function UrlExists(url, cb){
    $.ajax({
        url:      url,
        dataType: 'text',
        type:     'GET',
		async: 		false,
        error:  function(xhr){
            if(typeof cb === 'function')
			{
				cb.apply(this, [xhr.data.status]);
			}
        }
    });
}

function dialog(object2, cb, params)
{
	if(typeof object2 !="object")
	{
		object2 ={};
	}
	var  object1 ={
		buttons: [
			{
				text: "close",
				click: function() {
					if(typeof cb =="function")
					{
						cb(params);
					}
					$( this ).dialog( "close" );
				}
			}
		]
	};
	$.extend( object1, object2 );
	if($( "#dialog").length ==1)
	{
		$( "#dialog p").text(object1.message); 
		$( "#dialog").dialog(object1);
	}else
	{
		$( "#dialog p", parent.document).text(object1.message); 
		$( "#dialog" , parent.document).dialog(object1);
	}
	// $('.ui-dialog').css({left:"875px"});
	// $('.ui-dialog',parent.document).css({left:"875px"});
}

var bodyCtrl = function($scope, $compile, $cookies, apiService, Websokect)
{
	$(document).scrollTop(0) ;
	$scope.panelShow = false;
	$scope.admin_sess = $cookies.get('admin_sess');
	if(typeof $scope.admin_sess =="undefined")
	{
		location.href='/admin/login.html';
	}
	
	$scope.socket_push_data ={};
	$scope.templates ={};
	$scope.sidebarMenuList={};
	$scope.playSound = false;
	
	
	$scope.sidebar_menu_click = function(control, child)
	{
		$('#myTab li').removeClass('active');
		var index = $('#myTab li').length;
		var target=$('#myTab');
		var li ='<li role="presentation" data-tabindex="'+index +'" class="active">';
		li +='<a href="#tab_content'+index+'" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">'+child.pe_name ;
		li +='<i ng-click="tableClose('+index+') ;$event.preventDefault();" class="fa fa-close tabclose'+index+'"></i>';
		li +='</a></li>';
		target.append($compile(li)($scope));
		var target =$('#myTabContent');
		$('.tab-pane').removeClass('active in');
		$scope.templates[index] ={'url':child.pe_page,"control":control,"func":child.pe_func};
		var height = $(window).height()-187;
		// var height = 960;
		var tabpanel   	= '<div ng-init="tableindex='+index+'" role="tabpanel" data-tabindex="'+index +'" class="tab-pane fade" id="tab_content'+index+'" >';
		    tabpanel  	+='<iframe  height="'+height+'px" src="/admin/views/tabpanel.html#!/'+control+'/'+child.pe_func+'/'+$scope.templates[index].url+'/'+index+'/'+child.pe_id+'" scrolling="auto"   frameBorder="0"></iframe>';
			tabpanel 	+='</div>';
		target.append($compile(tabpanel)($scope));
		$('.tab-pane').eq(index).addClass('active in');
		$scope.panelShow = true;
	}
	
	
	// var socket = Websokect.open();
	$scope.init = function()
	{
		var promise = apiService.adminApi('AdminApi','getUser');
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.sidebarMenuList = r.data.body.menu_list;
					$scope.socket_push_data.top = r.data.body.socket_push_data;
					$scope.admin = r.data.body.admin_user;
					$scope.sidebar_menu_click($scope.sidebarMenuList[0].pe_control, $scope.sidebarMenuList[0].child[0]);
				}else if(r.data.status =="006")
				{
					var obj =
					{
						'message' :'please login',
						buttons: [
							{
								text: "close",
								click: function() {
									location.href="/admin/login.html"
									$( this ).dialog( "close" );
								}
							}
						]
					};
					dialog(obj);
				}else{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
				}
			

				var uid = 'system';
				// socket.on('connect', function(){
					// console.log('d');
					// socket.emit('login', uid);
				// });
			},
			function() {
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
		
	}
	
	
	$scope.processingCount = function(data)
	{
		data = (angular.fromJson(data));
		$scope.$apply(function() {
			$scope.socket_push_data.top  = data;
			if(data.processingCount.value > 0 && $scope.playSound == false)
			{
				$.playSound("/mp3/calltuktulk.mp3");
				var obj ={
					'message' :'Customer Call TukTuk',
					buttons: [
						{
							text: "close",
							click: function() {
								$.stopSound();
								$scope.playSound = false;
								$( this ).dialog( "close" );
								
							}
						}
					]
				};
				dialog(obj);
			}
		});
	}
	// socket.on('processingCount',$scope.processingCount);
	

	
	$scope.callAdminCallTukTuk = function()
	{
	
		if($scope.playSound == false)
		{
			$scope.playSound = true;
		}
		$.playSound("/mp3/calltuktulk.mp3");
		var obj ={
			'message' :'Customer Call TukTuk',
			buttons: [
				{
					text: "close",
					click: function() {
						$.stopSound();
						$scope.playSound = false;
						$( this ).dialog( "close" );
						// window.location.reload();
					}
				}
			]
		};
		dialog(obj);
	}
	// socket.on('callAdminCallTukTuk',$scope.callAdminCallTukTuk);
	
	
	 
	$scope.sidebarMenuListInit = function()
	{	
		$SIDEBAR_MENU = $("#sidebar-menu");
		$MENU_TOGGLE = $("#menu_toggle") ;
		init_sidebar();
	}
	
	$scope.tableClose = function(index)
	{
		$('#myTab li[data-tabindex='+index+']').prev().addClass('active');
		var pervtabindex = $('#myTab li[data-tabindex='+index+']').prev().data('tabindex');
		$('#tab_content'+pervtabindex).eq(0).addClass('active');
		$('#tab_content'+pervtabindex).addClass('in');
		$('#myTab li[data-tabindex='+index+']').remove();
		$('div[role=tabpanel][data-tabindex='+index+']').remove();
		delete $scope.templates[index];
		if( $('#myTab li').length ==0)
		{
			$scope.panelShow = false;
		}
	}
	
}

var MainController = function($scope, $routeParams, apiService, $templateCache, $compile, $cookies,Websokect)
{
	
	// var socket = Websokect.open();
	$templateCache.removeAll();
	$scope.data =
	{
		table_pageinfo :{},
		table_row:{},
		table_fields:{},
		table_pageinfo:{},
		table_form:{},
		table_search:{},
		table_order:{},
		form:{},
		form_row :{},
		pe_id :$routeParams.pe_id,
		tabindex: $routeParams.tabindex,
		table_action_list :{},
		row:{},
		socket_push_data:{},
	};
	$scope.user_terminal = user_terminal;


	$scope.addTr = function()
	{
		$scope.data.tableTr.push({});
	}
	
	$scope.sumTotal = function(subtotal,currency)
	{
		var total = {
			usd:0,
			khr:0,
		};

        angular.forEach(subtotal, function(value,key)
		{
			value = value || 0;
			total[$scope.data.row.currency[key]]+=value;
        });

		total.usd = total.usd.toFixed(2);
		total.khr = total.khr.toFixed(2);

		$scope.data.sumTotal = total
	}
	
	$scope.tableInit = function()
	{
		$scope.data.tableTr =[
			{},
			{},
			{},
			{},
			{},
			{},
			{},
			{},
			{},
			{},
		];
	}
	
	$scope.getFormPage = function()
	{
		
	}
	
	$scope.actionClick = function(row,id)
	{
		// console.log(row);
		var url ="/admin/views/form_panel.html#!/formEdit/"+row.pe_control+'/'+row.pe_func+'/'+row.pe_page+'/'+row.pe_id+'/'+id ;
		// console.log(url);
		
		location.href=url;
	}
	
	$scope.edit = function(id)
	{
		var url ="/admin/views/form_panel.html#!/formEdit/"+$scope.data.form.table_edit+$routeParams.pe_id +'/'+id;
		// console.log(url);
		location.href=url;
	}
	
	$scope.del  = function(id)
	{
		var obj ={
			'message' :'confirm delete',
			'title'	  :'confirm',
			buttons: 
			[
				{
					text: "YES",
					click: function() {
						$( this ).dialog( "close" );
						var controller = $routeParams.controller;
						var obj ={
							id :id
						}
						if($scope.ajaxload ==true)
						{
							var obj =
							{
								'message' :'loading...',
							};
							dialog(obj);
							$scope.search();
						}
						$scope.ajaxload = true;
						var promise = apiService.adminApi(controller,$scope.data.form.table_del, obj, $routeParams.pe_id);
						promise.then
						(
							function(r) 
							{
								$scope.ajaxload = false;
								if(r.data.status =="200")
								{
									var obj =
									{
										'message' :'delete ok',
									};
									dialog(obj);
									$scope.search();
								}else
								{
									var obj =
									{
										'message' :r.data.message,
									};
									dialog(obj);
								}
								
							},
							function() {
								$scope.ajaxload = false;
								var obj ={
									'message' :'system error'
								};
								dialog(obj);
							}
						)
					}
				},
				{
					text: "NO",
					click: function() {
						$( this ).dialog( "close" );
					}
				},
			]
		};
		dialog(obj);
	}
	
	$scope.changePurchaseItem = function(id)
	{
		if(id == 'null')
		{
			return  null;
		}
		return  $scope.data.form_row.itemList[id].unit;
	}
	
	$scope.editFormInit = function(func)
	{

		var controller = $routeParams.controller;
		var obj={
			id : $routeParams.id
		}
		var promise = apiService.adminApi(controller,func, obj,$routeParams.pe_id);
		promise.then
		(
			function(r) 
			{
				if(r.data.status =="200")
				{
					$scope.data.form_row = r.data.body.row.info;
					var sess = $cookies.get('admin_sess');
					if(typeof r.data.body.row.form !="undefined")
					{
						$scope.data.form.action=r.data.body.row.form.action+"?sess="+sess+"&pe_id="+r.data.body.row.form.pe_id;
					}
					$scope.data.form.info  = r.data.body.row.info;
					$scope.data.form.id = $routeParams.id;
					// $scope.data.form.action="/"+controller+"/doEdit?sess="+sess+"&pe_id="+$routeParams.pe_id;
					if(typeof r.data.body.row.operation !="undefined")
					{
						$scope.data.form_row.operation = r.data.body.row.operation;
						var div ='<my-map  zoom="15" lat="'+$scope.data.form_row.r_lat+'" lng="'+$scope.data.form_row.r_lng+'"></my-map>';
						$('.myGamp').append($compile(div)($scope));
					}
				}else
				{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
				}
			},
			function() {
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	$scope.addFormInit = function(initFunc)
	{
		var sess = $cookies.get('admin_sess');
		$scope.data.form.action ='/'+$routeParams.controller+'/'+$routeParams.func+'?sess='+sess+'&pe_id='+$routeParams.pe_id;
		$scope.data.form.id  = $routeParams.id;
		if(typeof initFunc !="undefined")
		{
			var obj ={
				id :$routeParams.id
			}
			var promise = apiService.adminApi($routeParams.controller,initFunc, obj,$routeParams.pe_id);
			promise.then
			(
				function(r) 
				{
					if(r.data.status =="200")
					{
						$scope.data.form.controller_list = r.data.body.controller_list;
					}else
					{
						var obj =
						{
							'message' :r.data.message,
						};
						dialog(obj);
					}
					
				},
				function() {
					var obj ={
						'message' :'system error'
					};
					dialog(obj);
				}
			)
		}
	}
	
	$scope.$watch('data.table_search.date_start', function(newValue, oldValue) 
	{
		if(newValue > $scope.data.table_search.date_end && typeof newValue !="undefined" && newValue !="null")
		{
			$scope.data.table_search.date_start = oldValue;
			var obj ={
				'message' :'起始時間大於結束時間'
			};
			dialog(obj);
			return false;
		}
	});
	
	$scope.$watch('data.table_search.date_end', function(newValue, oldValue) {
	
		if(newValue < $scope.data.table_search.date_start && typeof newValue !="undefined" && newValue !="null")
		{
			$scope.data.table_search.date_end = oldValue;
			var obj ={
				'message' :'起始時間大於結束時間'
			};
			dialog(obj);
			return false;
		}
	});
	
	$scope.tableListInit = function()
	{
		$scope.search();
	}
	
	$scope.searchClick = function()
	{
		$scope.search();
	}

	$scope.back = function()
	{
		window.history.back();
	}

	$scope.order = function(field)
	{
	
		if(typeof $scope.data.table_order[field] =='undefined' || $scope.data.table_order[field]=='')
		{
			$scope.data.table_order[field] = 'DESC';
		}else if($scope.data.table_order[field] == 'ASC')
		{
			$scope.data.table_order[field] = 'DESC';
		}else
		{
			$scope.data.table_order[field] ='ASC';
		}
		$scope.search();
	}
	
	
	$scope.tableClose = function()
	{
		var parent =window.parent.document; 
		var tabindex = $routeParams.tabindex;
		$(parent).find('.tabclose'+tabindex ).click();
	}
	$scope.pagePrevious = function()
	{
		if($scope.data.table_pageinfo.p  ==1)
		{
			return false;
		}
		$scope.data.table_pageinfo.p -=1;
		$scope.search();
	}

	
	$scope.searchReset = function()
	{
		$scope.data.table_search={};
		$scope.data.table_order={};
		$scope.data.table_pageinfo.p =1;
		$scope.data.table_pageinfo.limit = '25';
		$scope.search();
	}
	
	$scope.pageNext = function()
	{
		if($scope.data.table_pageinfo.p  ==$scope.data.table_pageinfo.pages)
		{
			return false;
		}
		$scope.data.table_pageinfo.p +=1;
		$scope.search();
	}
	
	$scope.changeLimit = function()
	{
		$scope.data.table_pageinfo.p =1;
		$scope.search();
	}
	
	$scope.changePage =function(p)
	{
		if($scope.data.table_pageinfop ==p)
		{
			return false;
		}
		$scope.data.table_pageinfo.p =p;
		$scope.search();
	}
		
	$scope.tableAdd = function()
	{
		var tabindex = $routeParams.tabindex;
		var url ="/admin/views/form_panel.html#!/formAdd/"+$scope.data.form.table_add+tabindex+'/'+$routeParams.pe_id;
		if(typeof $routeParams.id !="undefined")
		{
			url+="/"+$routeParams.id;
		}
		location.href=url;
	}		
		
	$scope.select_search = function(field,value)
	{
		$scope.search();
	}
		
	$scope.init = function(initFunc)
	{
		var sess = $cookies.get('admin_sess');
		$scope.data.form.action ='/'+$routeParams.controller+'/'+$routeParams.func+'?sess='+sess+'&pe_id='+$routeParams.pe_id;
		if(typeof initFunc !="undefined")
		{
			var obj ={
				id :$routeParams.id
			}
			var promise = apiService.adminApi($routeParams.controller,initFunc, obj,$routeParams.pe_id);
			promise.then
			(
				function(r) 
				{
					if(r.data.status =="200")
					{
						$scope.data.row = r.data.body.row;
						// console.log($scope.data);
					}else
					{
						var obj =
						{
							'message' :r.data.message,
						};
						dialog(obj);
					}
					
				},
				function() {
					var obj ={
						'message' :'system error'
					};
					dialog(obj);
				}
			)
		}
	}
		
	$scope.dateTimeSearchInit = function(c)
	{
		$( "."+c ).datetimepicker({
			"dateFormat":"yy-mm-dd",
			"timeFormat": "HH:mm"
		})
	}	
	
	$scope.dateSearchInit = function(c)
	{
		$( "."+c ).datepicker({
			"dateFormat":"yy-mm-dd"
		})
	}	
	
	$scope.clickbutton = function(row)
	{
		if($scope.ajaxload ==true)
		{
			var obj =
			{
				'message' :'loading...',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		var obj={
		}
		var promise = apiService.adminApi(row.pe_control,row.pe_func, obj, row.pe_id);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
					$scope.search();
				}else
				{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
				}
				
			},
			function() {
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
				$scope.ajaxload = false;
			}
		)
	}
	
	$scope.search = function()
	{
		if( typeof $('.date_start').val() !="undefined")
		{
			var time  = $('.date_start').val();
			var timeStart =time.replace(/-/g,'/').replace('T',' ');
			$scope.data.table_search.datetime_start_str =timeStart;
		}
		
		if( typeof $('.date_end').val() !="undefined")
		{
			var time  = $('.date_end').val();
			var timeEnd =time.replace(/-/g,'/').replace('T',' ');
			$scope.data.table_search.datetime_end_str =timeEnd;
		}
		
		var controller = $routeParams.controller;
		var pe_id = $routeParams.pe_id;
		var func = $routeParams.func;
		var obj={
			p :$scope.data.table_pageinfo.p,
			limit :$scope.data.table_pageinfo.limit,
			id:$routeParams.id
		}
		obj = $.extend(obj, $scope.data.table_search);
		obj.order = $scope.data.table_order;
		var promise = apiService.adminApi(controller,func, obj, pe_id);
		promise.then
		(
			function(r) 
			{
				if(r.data.status =="200")
				{
					$scope.data.table_row= r.data.body.list;
					$scope.data.table_title =r.data.title;
					$scope.data.table_form =r.data.body.form;
					$scope.data.table_pageinfo= r.data.body.pageinfo;
					$scope.data.table_pageinfo.start=(parseInt($scope.data.table_pageinfo.p)-1)*parseInt($scope.data.table_pageinfo.limit)+1;
					$scope.data.table_pageinfo.end=(parseInt($scope.data.table_pageinfo.p)-1)*parseInt($scope.data.table_pageinfo.limit)+parseInt($scope.data.table_row.length);
					$scope.data.table_fields = r.data.body.fields;
					$scope.data.table_action_list = r.data.body.action_list;
					$scope.data.table_button_list = r.data.body.button_list;
					$scope.data.form.table_add = r.data.body.form.table_add;
					$scope.data.form.table_del = r.data.body.form.table_del;
					$scope.data.form.table_edit = r.data.body.form.table_edit;
					$scope.data.table_subtotal_fields = r.data.body.subtotal_fields;
					$scope.data.table_subtotal_data = r.data.body.pageinfo.subtotal_datalist;
				}else
				{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
				}
				
			},
			function() {
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
}

var loginCtrl = function($scope, $cookies, apiService)
{
	$scope.ajaxload = false;
	$scope.setting = 
	{
		controller 	:'AdminApi',
		func 		:'login'
	};
	$scope.data =
	{
		input :{},
		response:{}
	};
	
	$scope.loginDone = function()
	{
		location.href="/admin/";
	}
	
	$scope.login = function(){
		var obj = 	$scope.data.input;
		if($scope.ajaxload ==true)
		{
			var obj ={
				'message':'isloading....',
			}
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		var promise = apiService.adminApi($scope.setting.controller,$scope.setting.func,obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.sess = r.data.body.sess;
					$cookies.put('admin_sess', $scope.sess, { path: '/' ,expires:new Date(new Date().getTime()+6000*60*12)});
					var obj =
					{
						'message':'welcome',
					};
					dialog(obj, $scope.loginDone);
				}else
				{
					var obj =
					{
						'message' :r.data.message,
					};
					dialog(obj);
				}
				
			},
			function() {
				$scope.ajaxload = false;
				var obj ={
					'message' :'system error'
				};
				 dialog(obj);
			}
		)
	}
}

adminApp.controller('bodyCtrl',  ['$scope', '$compile', '$cookies' ,'apiService' ,'Websokect' , bodyCtrl])
adminApp.controller('loginCtrl',  ['$scope', '$cookies' ,'apiService', loginCtrl]);
adminApp.controller('MainController',  ['$scope' ,'$routeParams', 'apiService' ,'$templateCache', '$compile', '$cookies','Websokect', MainController])



var apiService = function($http, $cookies)
{
	return {
		adminApi :function(controller, func, obj, pe_id){
			var sess = $cookies.get('admin_sess');
			var default_obj = {
					
			};
			var object = $.extend(default_obj, obj);
			return $http.post('/Api/'+controller+'/'+func+'?sess='+sess+'&pe_id='+pe_id, object ,  {headers: {'Content-Type': 'application/json'} });
		}
    };
}
adminApp.factory('apiService', ['$http', '$cookies', apiService]);
adminApp.config(function($routeProvider){
	$routeProvider.when("/",{
		templateUrl: function(params) {
			// return 'views/';
		},
		controller: 'MainController'
    }).when("/formAdd/:controller/:func/:page/:tabindex/:pe_id/:id",{
		templateUrl: function(params) {
					
			var page ='/admin/views/'+params.page+'.html?'+Math.random();
			return page;
		},
		cache: false,
		controller: 'MainController'
    }).when("/formAdd/:controller/:func/:page/:tabindex/:pe_id",{
		templateUrl: function(params) {
					
			var page ='/admin/views/'+params.page+'.html?'+Math.random();
			return page;
		},
		cache: false,
		controller: 'MainController'
    }).when("/formEdit/:controller/:func/:page/:pe_id/:id",{
		templateUrl: function(params) {
			// console.log(params);
			var page ='/admin/views/'+params.page+'.html?'+Math.random();
			return page;
		},
		cache: false,
		controller: 'MainController'
    }).when("/:controller/:func/:page/:tabindex/:pe_id/:id",{
		templateUrl: function(params) {
			
			var page ='/admin/views/'+params.page+'.html?'+Math.random();
			return page;
		},
		cache: false,
		controller: 'MainController'
    }).when("/:controller/:func/:page/:tabindex/:pe_id",{
		templateUrl: function(params) {
			var page ='/admin/views/'+params.page+'.html?'+Math.random();
			return page;
		},
		cache: false,
		controller: 'MainController'
    }).otherwise({redirectTo : '/'})
});

adminApp.directive('ngEnter', function() {
    return function(scope, elem, attrs) {
      elem.bind("keydown keypress", function(event) {
        if (event.which === 13) {
          scope.$apply(function() {
            scope.$eval(attrs.ngEnter);
          });

          event.preventDefault();
        }
      });
    };
});

adminApp.directive('myMap', function() {
	return {
		replace: true,
		scope: {
			lat :'@',
			lng :'@',
			zoom: '@'
		},
		restrict: 'E',
		template: '<div><div class="mapdiv" style="width:100%; height: 400px;"></div><input type="hidden" name="lat" value="{{lat}}"><input type="hidden" name="lng" value="{{lng}}"></div>',
		controller: function($scope, $element, $attrs, $compile) 
		{
        },link: function(scope, element, attrs)
		{
			var map;
			var center_point  ={lat: parseFloat(scope.lat), lng:  parseFloat(scope.lng)};
			var mapdiv = $(element).find('.mapdiv')[0];
			map = new google.maps.Map(mapdiv, {
				center: center_point ,
				zoom: parseInt(scope.zoom),
				scrollwheel: false,
				disableDefaultUI: true,
				zoomControl: true,
			});
			google.maps.event.clearListeners(map, 'bounds_changed');
			var contentString ="<div>dorp my set position <br> or click map for set</div>";
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
			var marker = new google.maps.Marker({
				position: center_point ,
				map: map,
				draggable: true,
			});
			infowindow.open(map, marker);
			
			marker.addListener("dragstart", function() {
				infowindow.close();
			});
			
			marker.addListener("dragend", function(event) {
				infowindow.open(map, marker);
				$(element).find('input[name=lat]').val(event.latLng.lat());
				$(element).find('input[name=lng]').val(event.latLng.lng());
			});
			
			map.addListener("click", function(event) {
				
				var point ={
					lat:event.latLng.lat(),
					lng:event.latLng.lng()
				};

				marker.setMap(null);
				marker = new google.maps.Marker({
					position: point ,
					map: map,
					draggable: true,
				});
				infowindow.open(map, marker);
				map.setCenter(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));
				$(element).find('input[name=lat]').val(event.latLng.lat());
				$(element).find('input[name=lng]').val(event.latLng.lng());
			});
        }
	}
});



adminApp.directive('ngOpenSelect', function() {
	var week =[
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday'
	];
	var option ="";
	angular.forEach(week, function(value, key) {
		option+='<option value="'+value+'">'+value+'</option>';
	})
	var template ="";
	template+='<select name ="{{name}}"   style="width : 120px;  height:30px;  display:inline;" class="form-control OpenSelect">'+option+'</select>';
	template+='&nbsp;&nbsp;&nbsp;';
	template+='<input  ng-blur="checkTime()" value="{{start}}" style="width : 150px ; height:30px; display:inline;" required="required"  class="form-control operation_start"  type="time" name="start[]"  >';
	template+='&nbsp;&nbsp;&nbsp;';
	template+='<input  ng-blur="checkTime()" value="{{end}}" style="width : 150px ; height:30px; display:inline;" required="required"  class="form-control operation_end"   type="time" name="end[]" >';
	template+='&nbsp;&nbsp;&nbsp;';
	template+='<a href="#" class="delOpenDatetime" ng-click="del(); $event.preventDefault();">del</a>';
	template+='&nbsp;&nbsp;&nbsp;';
	template+='<a href="#" class="addOpenDatetime" ng-click="add(); $event.preventDefault();">add</a>';
	
	return {
		// replace: true,
		scope: {
			name: '@',
			start: '@',
			end: '@',
		},
		restrict: 'A',
		template: template,
		controller: function($scope, $element, $attrs, $compile) 
		{
			$scope.add = function()
			{
				if($('.OpenSelect').length >6)
				{
					var obj ={
						'message':'one week only 7 day',
					}
					dialog(obj);
					return false;
				}
				var div ='<div ng-open-Select name="{{name}}"></div>'
				$element.parent().append($compile(div)($scope));
				$('.delOpenDatetime').eq(0).show();
				$('.addOpenDatetime').hide();
				$('.addOpenDatetime').eq(0).show();
			};
			$scope.del = function()
			{
				$element.remove();
				if($('.delOpenDatetime').length ==1)
				{
					$('.delOpenDatetime').eq(0).hide();
					$('.addOpenDatetime').eq(0).show();
				}else
				{
					$('.addOpenDatetime').eq(0).show();
				}
			};
			$scope.checkTime = function()
			{
				angular.forEach($('input.operation_start'), function(value, key) {
					if($(value).val() !="" && $('input.operation_end').eq(key).val()!="" )
					{
						if($(value).val() >= $('input.operation_end').eq(key).val())
						{
							var obj ={
								'message':'start time over end time',
							}
							dialog(obj);
						}
					}
				});
			}
        },
		link: function(scope, element, attrs)
		{
			if($('.delOpenDatetime').length ==1)
			{
				$('.delOpenDatetime').eq(0).hide();
			}
			$('.addOpenDatetime').hide();
			$('.addOpenDatetime').eq(0).show();
        }
	}
});


adminApp.filter('range', function() {
  return function(page, total) {
    total = parseInt(total);
    for (var i=0; i<total; i++) {
		page.push(i);
    }

    return page;
  };
});

adminApp.factory('Websokect', ['$q', '$rootScope', '$http', function($q, $rootScope, $http) 
{
	return {
		open :function(){
			var socket = {};
			var uid ="system";
			var host =location.protocol + '//' + location.host ;
			socket = io(host+':2120', {secure: true});
			return socket;
		}
    };
}]);

$( document ).ready(function() {
	CURRENT_URL = window.location.href.split("#")[0].split("?")[0],
	$BODY = $("body");
	$SIDEBAR_FOOTER = $(".sidebar-footer");
	$LEFT_COL = $(".left_col");
	$RIGHT_COL = $(".right_col");
	$NAV_MENU = $(".nav_menu");
	$FOOTER = $("footer");
	randNum = function() {
		return Math.floor(21 * Math.random()) + 20
	};
});
