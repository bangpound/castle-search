rShiefSearch = angular.module('castleSearch', [
        'ajoslin.promise-tracker',
        'elasticjs.service',
        'dangle'
    ])
    .config(function($locationProvider) {
        $locationProvider.html5Mode(true);
    })
    .constant('ejsConfig', {server: rShief.baseurl + '/search/prod', tracker: 'searching'})
;
