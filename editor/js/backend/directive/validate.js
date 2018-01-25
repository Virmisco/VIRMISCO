(function () {
    angular
        .module('backend')
        .directive('validate', function (){
            return {
                require: 'ngModel',
                restrict: 'A',
                link: function(scope, elem, attributes, ngModel) {
                    var validatorFunction = scope;
                    angular.forEach(attributes.validate.split('.'), function(name) {
                        validatorFunction = validatorFunction[name];
                    });

                    //For DOM -> model validation
                    ngModel.$parsers.unshift(function(value) {
                        var valid = validatorFunction(value);
                        ngModel.$setValidity('validate', valid);
                        return valid ? value : undefined;
                    });

                    //For model -> DOM validation
                    //deactivated, caused errors
                    //ngModel.$formatters.unshift(function(value) {
                    //    ngModel.$setValidity(validatorFunction(value));
                    //    return value;
                    //});
                }
            };
        });
})();
