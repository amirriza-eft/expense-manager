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

<!--<div id="app">-->
<!---->
<!--    <div v-if="isLoggedIn">-->
<!--        <h1>Welcome back!</h1>-->
<!--        <h2>{{ message }}</h2>-->
<!---->
<!--        <p>Age: {{ age }}</p>-->
<!---->
<!--        <a :href="profileUrl">-->
<!--            My Profile-->
<!--        </a>-->
<!---->
<!--        <div v-for="transaction in transactions" :key="transaction.id">-->
<!---->
<!--            <h4>{{ transaction.title }}:</h4>-->
<!--            {{ transaction.amount }}T-->
<!---->
<!--        </div>-->
<!---->
<!--    </div>-->
<!---->
<!--    <div v-else>-->
<!--        <h1>Please login!</h1>-->
<!--    </div>-->
<!---->
<!--</div>-->



<div id="app">

    <h1>Transaction</h1>

    <form @submit.prevent="saveTransaction">

        <div>
            <label>Title</label>

            <input
                    type="text"
                    v-model="transactionTitle"
            >
        </div>

        <div>
            <label>Amount</label>

            <input
                    type="number"
                    v-model="amount"
            >
        </div>

        <div>
            <label>Type</label>

            <select v-model="type">

                <option value="expense">
                    Expense
                </option>

                <option value="income">
                    Income
                </option>

            </select>
        </div>

        <button type="submit">
            Save
        </button>

    </form>

    <hr>

    <p>Title: {{ transactionTitle }}</p>
    <p>Amount: {{ amount }}</p>
    <p>Type: {{ type }}</p>

</div>

<script>

    // const { createApp } = Vue;
    //
    // createApp({
    //
    //     data() {
    //         return {
    //             isLoggedIn: true,
    //             message: 'Amir Eft',
    //             age: 25,
    //             profileUrl: 'http://localhost:8000/profile',
    //
    //             transactions: [
    //                 {
    //                     id: 1,
    //                     title: 'Salary',
    //                     amount: 5000000
    //                 },
    //                 {
    //                     id: 2,
    //                     title: 'Food',
    //                     amount: 500000
    //                 },
    //                 {
    //                     id: 3,
    //                     title: 'Internet',
    //                     amount: 200000
    //                 }
    //             ]
    //         };
    //     }
    //
    // }).mount('#app');


    const { createApp } = Vue;

    createApp({

        data() {
            return {
                transactionTitle: '',
                amount: '',
                type: 'expense'
            };
        },

        methods: {

            saveTransaction() {

                console.log('Title:', this.transactionTitle);
                console.log('Amount:', this.amount);
                console.log('Type:', this.type);

            }

        }

    }).mount('#app');

</script>

</body>
</html>