var Main = Main || {};
(function(window, exports, undefined) {
    'use strict';

    let myWorker;

    exports.init = function () {
        // if (window.Worker) {
        //     initWorker();
        //     // post message
        //     myWorker.postMessage(['start']);
        // }
    };

    let initWorker = function () {
        myWorker = new Worker("/assets/js/worker.js");
        myWorker.onmessage = function(e) {
            console.log('Message received from worker');
        }
    };

})(window, Main);

if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    Main.init();
} else {
    document.addEventListener('DOMContentLoaded', Main.init);
}