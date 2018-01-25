(function () {
    angular
        .module('backend')
        .factory('uuid', uuid);

    /**
     * @class Uuid
     */
    function uuid() {
        return {createRandom: createRandom};

        /**
         * Creates a new UUID string.
         * @returns {string}
         * @memberOf Uuid#
         */
        function createRandom() {
            var pos;
            var result = '';
            for (pos = 0; pos < 36; pos++) {
                switch (pos) {
                    case 8:
                    case 13:
                    case 18:
                    case 23:
                        result += '-';
                        break;
                    case 14:
                        result += '4';
                        break;
                    case 19:
                        result += '89ab'.charAt(getRandom(4));
                        break;
                    default:
                        result += '0123456789abcdef'.charAt(getRandom(16));
                }
            }
            return result;
        }
        function getRandom(max) {
            return Math.random() * max;
        }
    }
})();
