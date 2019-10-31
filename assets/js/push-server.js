const webPush = require('web-push');

if (!process.env.VAPID_PUBLIC_KEY || !process.env.VAPID_PRIVATE_KEY) {
    console.log("You must set the VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY "+
        "environment variables. You can use the following ones:");
    console.log(webPush.generateVAPIDKeys());
    return;
}

webPush.setVapidDetails(
    'mailto:ms@flurp.de',
    process.env.VAPID_PUBLIC_KEY,
    process.env.VAPID_PRIVATE_KEY
);

module.exports = function(app, route) {
    app.get(route + 'vapidPublicKey', function(req, res) {
        res.send(process.env.VAPID_PUBLIC_KEY);
    });

    app.post(route + 'register', function(req, res) {
        res.sendStatus(201);
    });

    app.post(route + 'sendNotification', function(req, res) {
        const subscription = req.body.subscription;
        const payload = null;
        const options = {
            TTL: req.body.ttl
        };

        setTimeout(function() {
            webPush.sendNotification(subscription, payload, options)
                .then(function() {
                    res.sendStatus(201);
                })
                .catch(function(error) {
                    res.sendStatus(500);
                    console.log(error);
                });
        }, req.body.delay * 1000);
    });
};