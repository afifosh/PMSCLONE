import Echo from "laravel-echo"
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: broadcastKey,
    cluster: broadcastCluster,
    wsHost: broadcastHost,
    wsPort: broadcastPort,
    forceTLS:broadcastForceTLS == 1,
    disableStats: true,

});
