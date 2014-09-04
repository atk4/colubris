/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Client', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        clients: [],

        save: function ( client ) {

//            console.log(client);

            if (typeof client.id === 'undefined' ) {
                service.clients.push( angular.copy( client)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer(client);
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupClient();
        },
        remove: function(index) {
            API.removeOne(
                'client',
                null,
                {'id' : service.clients[index].id},
                function(obj) {
                    if (obj.result === 'success') {
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
            service.clients.splice(index, 1);
            $rootScope.$broadcast( 'clients.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupClient(index);
            $rootScope.$broadcast('client.update', service.clients[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.clients[index]);
            //$rootScope.$broadcast( 'requirements.update' );
        },
        cancel: function() {
            console.log('------> cancel');
            this.restoreClient();
            this.resetBackupClient();
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        saveOnServer: function(client) {
            API.saveOne(
                'client',
                null,
                {},
                angular.toJson(client),
                function(obj) {
                    if (obj.result === 'success') {
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
        },
        getFromServer: function() {
            API.getAll(
                'client',
                'getForClient',
                undefined,
                function(obj) {
                    service.clients = obj.data;
                    $rootScope.$broadcast( 'clients.update' );
                }
            );
        },
        backupClient: function(index) {
            current_index = index;
            service.clients[index].backup = angular.copy( service.clients[index]);
//            console.log(service.clients[current_index].backup);
        },
        resetBackupClient: function() {
            if (current_index) {
                service.clients[current_index].backup = {};
                current_index = null;
            }
        },
        restoreClient: function() {
            if (
                typeof service.clients[current_index] !== 'undefined' &&
                typeof service.clients[current_index].backup !== 'undefined'
            ) {
                service.clients[current_index] = service.clients[current_index].backup;
            }
        }
    }

  return service;
}]);