app.controller('queueCtrl', function ($scope, $modal, $filter, Data, $http, $location) {

    $scope.queuesList = [];
    $scope.userList = [];
    $scope.userListAfter = [];
    $scope.allLinuxUsers = [];


    //Getting all users List
    $scope.loadOverAllList = function(){
        $http({
            url:'json/getUsers.json',
            method: 'GET',
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            $scope.userList = response.users;
        })
        .error(function (response) {
            $scope.message = "Unable to reach the service.";
        });
    };
    $scope.loadOverAllList();


    //Getting overall List
    $scope.loadOverAllList = function(){
        $http({
            url:'http://localhost/myApp/queueList.php',
            //url:'json/getOverview.json',
            method: 'GET',
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            $scope.queuesList = response.queues;
        })
        .error(function (response) {
            $scope.message = "Unable to reach the service.";
        });
    };
    $scope.loadOverAllList();

    // getting Queues details with User Id
    $scope.getQueueDetailsByQueueName = function(qName){
        $scope.queueNameInd = qName;
        $scope.parentQNameInd = '';
        $scope.capacityInd = '';
        $scope.state = '';
        $scope.stateClass = '';
        $scope.userQueueTable = '';
        $scope.childQueueTable = '';

        angular.forEach ($scope.queuesList, function(val, key){
            if(val.qname == qName){
                if(val.pQueue == ""){
                    $scope.parentQNameInd = "Null";
                }
                else{
                    $scope.parentQNameInd = val.pQueue;
                }
                $scope.capacityInd = val.capacity;
                if(val.state == 'RUNNING'){
                    $scope.state = val.state;
                    $scope.stateClass = 'success';
                }
                if(val.state == 'STOP'){
                    $scope.state = val.state;
                    $scope.stateClass = 'danger';
                }
                $scope.userQueueTable = val.acl_submit_applications;
                if(val.queues == ""){
                    $scope.childQueueTable = "Null";
                }
                else{
                    $scope.childQueueTable = val.queues;
                }
                if(val.acl_submit_applications != ""){
                    $scope.userTemp = '';
                    $scope.userTempIndex = '';
                    $scope.userListAllTemp = [];
                    $scope.userTemp = val.acl_submit_applications;
                    $scope.userTempIndex = $scope.userTemp.split(",");

                    for (i = 0; i < $scope.userTempIndex.length; i++) {
                        $scope.userListAllTemp.push($scope.userTempIndex[i]);
                    }
                }
                else{
                    $scope.userListAllTemp = [];
                }
                $scope.startDragDrop($scope.userListAllTemp);
                jQuery('.addEditQueue').css("display", "block");
                jQuery('.addEditQueueUser').css("display", "block");
                jQuery('.create-button').css("display", "block");
            }
        });

    };

    $scope.startDragDrop = function(tempUser){
        $scope.tempLinuxUsers = tempUser;
        $scope.tempList = [];

        $http({
            url:'json/getLinuxUsers.json',
            method: 'GET',
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            $scope.allLinuxUsers = response.userListLinux;
            angular.copy($scope.allLinuxUsers, $scope.tempList);

            for (i = 0; i < $scope.tempLinuxUsers.length; i++) {
                for (j = 0; j < $scope.tempList.length; j++) {
                    if($scope.tempLinuxUsers[i] == $scope.tempList[j])
                    {
                        $scope.tempList.splice(j,1);
                    }
                }
            }
        })
        .error(function (response) {
            $scope.message = "Unable to reach the service.";
        });



        $scope.addText = "";


        $scope.dropSuccessHandler = function($event,index,array,val){
          array.splice(index,1);
          if(val == false){
            $scope.userListAfter = array;
          }
        };

        $scope.onDrop = function($event,$data,array){
          array.push($data);
          $scope.userListAfter = array;
        };

    };

    $scope.createXmlFile = function(queueName){
        $scope.temp = [];
        angular.copy($scope.allLinuxUsers, $scope.temp);

        for (i = 0; i < $scope.userListAfter.length; i++) {
            for (j = 0; j < $scope.temp.length; j++) {
                if($scope.userListAfter[i] == $scope.temp[j])
                {
                    $scope.temp.splice(j,1);
                }
            }
        }
        if($scope.userListAfter == ""){
            alert('Atleast modify one user to download the xml.');
        }
        else{
            var tempStr = '';
            for (i = 0; i < $scope.temp.length; i++) {
                if($scope.temp.length -1 == i){
                    tempStr += $scope.temp[i]
                }
                else{
                    tempStr = tempStr + $scope.temp[i] + ",";
                }
            }

            for (i = 0; i < $scope.queuesList.length; i++) {
                if(queueName == $scope.queuesList[i].qname){
                    $scope.queuesList[i].acl_submit_applications = tempStr;
                    console.log('sss',$scope.queuesList[i].acl_submit_applications);
                }
            }

            var tempObj = {"queues":[]};
            tempObj.queues = $scope.queuesList;
            $http({
                url: 'http://localhost/myApp/createFile.php',   ////need to put in the service URL
                method: 'POST',
                data: tempObj,
                headers: {'Content-Type': 'application/json'}
            })
            .success(function (response) {
                location.href = "http://localhost/myApp/downloadFile.php";
            })
            .error(function (response) {
                $scope.alertError.push({type: 'error-message', message: "The requested URL /compare was not found on this server."});
            })
        }
    };


});