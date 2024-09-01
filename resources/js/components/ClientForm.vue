<template>
    <div>
        <h1 class="mb-6">Clients -> Add New Client</h1>

        <div class="max-w-lg mx-auto">
            <div v-if="errors.length > 0" class="alert alert-danger">
                <ul>
                    <li v-for="error in errors">{{ error }}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" class="form-control" v-model="client.name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" class="form-control" v-model="client.email">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" class="form-control" v-model="client.phone">
            </div>
            <div class="form-group">
                <label for="name">Address</label>
                <input type="text" id="address" class="form-control" v-model="client.address">
            </div>
            <div class="flex">
                <div class="form-group flex-1">
                    <label for="city">City</label>
                    <input type="text" id="city" class="form-control" v-model="client.city">
                </div>
                <div class="form-group flex-1">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" class="form-control" v-model="client.postcode">
                </div>
            </div>

            <div class="text-right">
                <a href="/clients" class="btn btn-default">Cancel</a>
                <button @click="storeClient" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ClientForm',

    data() {
        return {
            client: {
                name: '',
                email: '',
                phone: '',
                address: '',
                city: '',
                postcode: '',
            },
            errors: [],
        }
    },

    methods: {
        storeClient() {
            this.errors = [];

            if(this.client.phone === '' && this.client.email === '') {
                this.errors.push('Phone or Email are required');
            }

            if(this.client.phone !== '') {
                if(new RegExp(/^[0-9+\s]*$/).test(this.client.phone) === false) {
                    this.errors.push('Phone must contain only numbers, spaces and + sign');
                }
            }

            if(this.client.email !== '') {
                if(new RegExp(/^[^\s@]+@[^\s@]+\.[^\s@]+$/).test(this.client.email) === false) {
                    this.errors.push('Email is not valid');
                }
            }

            if(this.errors.length > 0) {
                return;
            }

            axios.post('/clients', this.client)
                .then((data) => {
                    window.location.href = data.data.url;
                })
                .catch((error) => {
                    for(const [_, value] of Object.entries(error.response.data.errors)) {
                        this.errors.push(value[0]);
                    }
                });
        }
    }
}
</script>
