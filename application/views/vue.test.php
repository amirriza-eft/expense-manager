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

        <div v-for="transaction in transactions" :key="transaction.id">

            <h4>{{ transaction.title }}:</h4>
            {{ transaction.amount }}T


        </div>

    </div>

    <div v-else>
        <h1>Please login!</h1>
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
                profileUrl: 'http://localhost:8000/profile',

                transactions: [
                    {
                        id: 1,
                        title: 'Salary',
                        amount: 5000000
                    },
                    {
                        id: 2,
                        title: 'Food',
                        amount: 500000
                    },
                    {
                        id: 3,
                        title: 'Internet',
                        amount: 200000
                    }
                ]
            };
        }

    }).mount('#app');

</script>

</body>
</html>