<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vue Test</title>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>

<body>

<div id="app">
    <div v-if="isLoggedIn">
        <h1>Welcome back!</h1>
        <h2>{{ message }}</h2>

        <p>Age: {{ age }}</p>

        <a :href="profileUrl">
            My Profile
        </a>
    </div>
</div>

<script>

    const { createApp } = Vue;

    createApp({

        data() {
            return {
                isLoggedIn: true,
                message: 'Amir Eft',
                age: 25,
                profileUrl: 'http://localhost:8000/profile'
            };
        }

    }).mount('#app');

</script>

</body>
</html>