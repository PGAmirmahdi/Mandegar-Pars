/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
    apiKey: "AIzaSyCUdU7PnQmzrkcJDFOJsIGcpe7CZV1GBrA",
    authDomain: "mandegarpars-5e075.firebaseapp.com",
    projectId: "mandegarpars-5e075",
    storageBucket: "mandegarpars-5e075.appspot.com",
    messagingSenderId: "11452789862",
    appId: "1:11452789862:web:8ee1465cf4e374fcbde9a7"
    // measurementId: "G-XXXXX"
});

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "https://mpsystem.ir/assets/media/image/logo.png",
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});
