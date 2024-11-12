import './bootstrap';



import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

import { createApp } from 'vue';
import CalendarComponent from './Components/Calendar.vue';

const app = createApp({});
app.component('calendar-component', CalendarComponent);
app.mount('#app');

