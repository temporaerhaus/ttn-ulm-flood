onmessage = function(e) {
  console.log('Worker: Message received from main script');

  let command = e.data[0];

  if (command === 'start') {

    postMessage('started');

    // setInterval(function () {
    //   console.log('interval!');
    // }, 2000);
  }

};