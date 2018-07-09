'use strict';
var pokerInsuranceApp = angular.module("pokerInsuranceApp", ['ngRoute', 'ngCookies']);
pokerInsuranceApp.config(function($routeProvider)
{
	$routeProvider.when("/:controller/:func",
	{
		templateUrl: function(params) 
		{
			return 'views/'+params.controller+params.func+'.html';
		},
		controller: 'pageCtrl'
    }).when("/:controller/:func/:page",
	{
		templateUrl: function(params) 
		{
			return 'views/'+params.page+'.html';
		},
		controller: 'pageCtrl'
    }).when("/admin",
	{
		redirectTo:"/admin"
    }).otherwise(
		{
			controller: 'Error404Controller',
			templateUrl: 'views/Error404.html'
		}
	)
});

var LoginCtrl = function($scope ,$routeParams, apiService,$cookies)
{
	$scope.init = function(){
		
	}
}
pokerInsuranceApp.controller('LoginCtrl',  ['$scope' ,'$routeParams', 'apiService' ,'$cookies', LoginCtrl]);


var pageCtrl = function($scope ,$routeParams, apiService, $cookies, Websokect)
{
	
	$scope.data =
	{
		input :{},
		urlParams:{},
		response :{},
		user_delivery:{}
	}

	var socket = Websokect.open();
	
	$scope.tuktukShowMap = function ()
	{
		$( "#users-contain" ).dialog({
			height: 400,
			width: 350,
			modal: true
		});
		// dialog.dialog( "open" );
	}
	
	$scope.tuktukinit = function ()
	{
		var promise = apiService.Api('/Api/TukTuk/getUser');
		promise.then
		(
			function(r) 
			{
			
				if(r.data.status =="200")
				{
					
					
					var tuktukid = 'tuktuk'+r.data.body.tuktukid;
					$scope.data.order = r.data.body.order
					socket.on('connect', function(){
						socket.emit('login', tuktukid);
					});
					callRute();
					callRute2();
				}
			},
			function() 
			{
				
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.userinit = function ()
	{
		var promise = apiService.Api('/Api/User/getUser');
		promise.then
		(
			function(r) 
			{
				
				if(r.data.status =="200")
				{
					
					$scope.data.islogin = r.data.body.islogin;
					
					var uid = 'user'+r.data.body.uid;
					socket.on('connect', function(){
						socket.emit('login', uid);
					});
					$scope.data.step = 1;
					// console.log(r.data.body.user_delivery);
					if( r.data.body.user_delivery  != null)
					{
						
						
						switch(r.data.body.user_delivery.status)
						{
							case "calltuktuk":
								$scope.data.step=4;
								break;
							case "processing":
								$scope.data.step=4;
								break;
							case "cancel" :
								$scope.data.step=3;
								break;
							case "tuktukgo":
								$scope.data.step=5;
								$scope.data.user_delivery =r.data.body.user_delivery ;
								break;
							default:
						}			
						return;
					}else if(r.data.body.islogin ==1)
					{
						if(r.data.body.user_status =="registered")
						{
							$scope.data.input.phone = r.data.body.user_phone;
							$scope.data.input.vid = r.data.body.verifycode.id;
							$scope.data.step =2;
						}
						else if(r.data.body.user_ticket >0)
						{
							$scope.data.step =3;
						}else
						{
							$scope.data.step =6;
						}
					}
				}
			},
			function() 
			{
				
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.CallUserTukTukGo = function(r)
	{
		
		r= JSON.parse(r);
		$scope.$apply(function() {
			$scope.data.step = 5;
			$scope.data.user_delivery.phone= r.phone;
			$scope.data.user_delivery.tuktuk_id= r.tuktuk_id;
			
		});
	}
	socket.on('CallUserTukTukGo',$scope.CallUserTukTukGo);
	
	$scope.CallTukTukPush = function(r)
	{
		r= JSON.parse(r);
		// console.log(r);
		// $scope.$apply(function() {
			// $scope.data.step = 5;
			// $scope.data.user_delivery.phone= r.phone;
			// $scope.data.user_delivery.tuktuk_id= r.tuktuk_id;
			
		// });
	}
	socket.on('CallTukTukPush',$scope.CallTukTukPush);

	$scope.CallUserCancel = function(r)
	{
		
		$scope.$apply(function() {
			$scope.data.step = 4;
			// $scope.data.user_delivery.phone= r.phone;
			// $scope.data.user_delivery.tuktuk_id= r.tuktuk_id;
			
		});
	}
	socket.on('CallUserCancel',$scope.CallUserCancel);
	
	var strUrl = location.href;
	var getPara, ParaVal;
	var aryPara = [];
	if (strUrl.indexOf("?") != -1) 
	{
		var getSearch = strUrl.split("?");
		getPara = getSearch[1].split("&");
		for (var i = 0; i < getPara.length; i++) 
		{
			ParaVal = getPara[i].split("=");
			$scope.data.urlParams[ParaVal[0]] = ParaVal[1];
		}
	}
	
	$scope.calltuktuk = function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/calltuktuk', $scope.data.urlParams);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.response = r.data.body;
					var obj ={
						'message' :r.data.message
					};
					dialog(obj);
					$scope.data.step = 4;
					
				}else
				{
					var obj ={
					'message' :r.data.message
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
	
	$scope.tuktuklogin = function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/'+$routeParams.func, $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.islogin = r.data.body.islogin;
					var obj ={
						'message' :r.data.message,
						buttons: [
							{
								text: "close",
								click: function() {
									$( this ).dialog( "close" );
									location.href="/#!/TukTuk/index"
								}
							}
						]
					};
					dialog(obj);
				}else
				{
					var obj ={
					'message' :r.data.message
					};
					dialog(obj);
				}
				
			},
			function() 
			{
				$scope.ajaxload = false;
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.login =  function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/login', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.islogin = r.data.body.islogin;
					// var obj ={
						// 'message' :r.data.message
					// };
					var obj =
					{
						'message' :'login ok',
						buttons: [
							{
								text: "close",
								click: function() {
									// location.href="/admin/login.html"
									history.go(-1);
									$( this ).dialog( "close" );
								}
							}
						]
					};
					dialog(obj);
				}else
				{
					var obj ={
					'message' :r.data.message
					};
					dialog(obj);
				}
				
			},
			function() 
			{
				$scope.ajaxload = false;
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.init = function(func)
	{
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/'+func, $scope.data.urlParams);
		promise.then
		(
			function(r) 
			{
				
				if(r.data.status =="200")
				{
					$scope.data.response = r.data.body
					if($scope.data.response.isuse =='0')
					{
						// $scope.data.step=3;
					}
					
				}else
				{
					var obj ={
					'message' :r.data.message
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
	
	$scope.starttimer = function()
	{
		$scope.timer = setInterval(function()
		{
			$scope.data.countdown++;
			if($scope.data.countdown > 60)
			{
				$scope.data.countdown = 0;
				$scope.data.resenddisabled =false;
				window.clearInterval($scope.timer);
			}
			$scope.$apply();
		}, 1000);
	}
	
	$scope.resendVerifyCode = function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/resendVerifyCode', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.resenddisabled = true;
					$scope.data.vid = r.data.body.id;
					$scope.starttimer();

					var obj ={
						'message' :r.data.message
					};
					dialog(obj);
				}else
				{
					var obj ={
					'message' :r.data.message
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
	
	$scope.verifyCode = function()
	{
		
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/verifyCode', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.step=3;
				}else
				{
					var obj ={
						'message' :r.data.message
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
	$scope.singup = function()
	{
		if($scope.data.input.password != $scope.data.input.c_password)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		var obj = $scope.data.input;
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/'+$routeParams.func, obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.step=2;
					var user = r.data.body.user;
					$scope.data.input.id = user.id;
					$scope.data.input.vid = user.vid;
					$scope.data.resenddisabled = true;
					$scope.starttimer();
				}else
				{
					var obj ={
					'message' :r.data.message
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
pokerInsuranceApp.controller('pageCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', 'Websokect' , pageCtrl]);

var bodyCtrl = function($scope ,$routeParams, apiService, $cookies)
{
	
}
pokerInsuranceApp.controller('bodyCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', bodyCtrl]);

var Error404Controller = function($scope ,$routeParams, apiService, $cookies)
{
	
}
pokerInsuranceApp.controller('Error404Controller',  ['$scope' ,'$routeParams', 'apiService','$cookies', Error404Controller]);

var apiService = function($http,$cookies)
{
	var sess = $cookies.get('usess');
	return {
		Api :function(apiurl, obj)
		{
			var default_obj = 
			{
			
			};
			var object = $.extend(default_obj, obj);
			return $http.post(apiurl+'?sess='+sess , object,  {headers: {'Content-Type': 'application/json'}});
		}
    };
}
pokerInsuranceApp.factory('apiService', ['$http','$cookies', apiService]);


pokerInsuranceApp.factory('Websokect', ['$q', '$rootScope', '$http', function($q, $rootScope, $http) 
{
	return {
		open :function(){
			var socket = {};
			var host =location.protocol + '//' + location.host ;
			socket = io(host+':2120', {secure: true});
			return socket;
		}
    };
}]);