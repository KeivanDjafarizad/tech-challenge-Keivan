<template>
    <div>
        <h1 class="mb-6">Clients -> {{ client.name }}</h1>

        <div class="flex">
            <div class="w-1/3 mr-5">
                <div class="w-full bg-white rounded p-4">
                    <h2>Client Info</h2>
                    <table>
                        <tbody>
                            <tr>
                                <th class="text-gray-600 pr-3">Name</th>
                                <td>{{ client.name }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Email</th>
                                <td>{{ client.email }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Phone</th>
                                <td>{{ client.phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Address</th>
                                <td>{{ client.address }}<br/>{{ client.postcode + ' ' + client.city }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-2/3">
                <div>
                    <button class="btn" :class="{'btn-primary': currentTab === 'bookings', 'btn-default': currentTab !== 'bookings'}" @click="switchTab('bookings')">Bookings</button>
                    <button class="btn" :class="{'btn-primary': currentTab === 'journals', 'btn-default': currentTab !== 'journals'}" @click="switchTab('journals')">Journals</button>
                </div>

                <!-- Bookings -->
                <div class="bg-white rounded p-4" v-if="currentTab === 'bookings'">
                    <div class="d-flex justify-content-between">
                        <h3 class="mb-3">List of client bookings</h3>
                        <div>
                            <select v-model="bookingsShown">
                                <option value="all">All</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="past">Past</option>
                            </select>
                        </div>
                    </div>

                    <template v-if="filteredBookings && filteredBookings.length > 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in filteredBookings" :key="booking.id">
                                    <td>{{ formatBooking(booking.start, booking.end) }} </td>
                                    <td>{{ booking.notes }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" @click="deleteBooking(booking)">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>

                    <template v-else>
                        <p class="text-center">The client has no bookings.</p>
                    </template>

                </div>

                <!-- Journals -->
                <div class="bg-white rounded p-4" v-if="currentTab === 'journals'">
                    <div class="d-flex justify-content-between mb-2">
                        <h3 class="mb-3">List of client journals</h3>
                        <button class="btn btn-outline-primary" @click="journalFormVisible = true">
                            New Journal
                        </button>
                    </div>

                    <form @submit.prevent="submitNewJournal" v-if="journalFormVisible" class="mb-2">
                        <div class="form-group">
                            <label for="entry_date">Entry Date</label>
                            <input type="date" id="entry_date" class="form-control" v-model="newJournal.entry_date">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea id="notes" class="form-control" v-model="newJournal.notes"></textarea>
                        </div>
                        <button class="btn btn-primary">Save</button>
                        <button class="btn btn-danger" @click="journalFormVisible = false">Cancel</button>
                    </form>

                    <template v-if="this.journals && this.journals.length > 0">
                        <table class="w-100">
                            <thead>
                            <tr>
                                <th>Notes</th>
                                <th>Entry Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="journalEntry in this.journals" :key="journalEntry.id">
                                <td>{{ formatJournalDate(journalEntry.entry_date) }}</td>
                                <td>{{ journalEntry.notes }} </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="deleteJournal(client, journalEntry)">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </template>

                    <template v-else>
                        <p class="text-center">The client has no bookings.</p>
                    </template>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from "moment";

export default {
    name: 'ClientShow',

    props: ['client'],

    data() {
        return {
            currentTab: 'bookings',
            bookingsShown: 'all',
            journalFormVisible: false,
            newJournal: {
                entry_date: '',
                notes: ''
            },
            journals: []
        }
    },

    computed: {
        filteredBookings() {
            if(this.bookingsShown === 'upcoming') {
                return this.client.bookings.filter(booking => new Date(booking.start) >= new Date());
            } else if(this.bookingsShown === 'past') {
                return this.client.bookings.filter(booking => new Date(booking.start) < new Date());
            } else {
                return this.client.bookings;
            }
        }
    },

    methods: {
        switchTab(newTab) {
            this.currentTab = newTab;
        },

        deleteBooking(booking) {
            axios.delete(`/bookings/${booking.id}`);
        },

        formatBooking(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            return moment(startDate).format('dddd D MMMM YYYY') + ', ' + moment(startDate).format('hh:mm') + ' to ' + moment(endDate).format('hh:mm');
        },

        formatJournalDate(date) {
            return moment(date).format('dddd D MMMM YYYY');
        },

        deleteJournal(client, journalEntry) {
            axios.delete(`${client.id}/journals/${journalEntry.id}`)
                .then(response => {
                    if(response.status === 204) {
                        this.journals = this.journals.filter(j => j.id !== journalEntry.id);
                        alert('Journal deleted successfully');
                    }
                });
        },

        submitNewJournal() {
            axios.post(`${this.client.id}/journals`, this.newJournal)
                .then(response => {
                    if(response.status === 201) {
                        this.journals.unshift(response.data.journal);
                        this.newJournal = {
                            entry_date: '',
                            notes: ''
                        };
                        this.journalFormVisible = false;
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },

    beforeMount() {
        this.journals = this.client.journals;
    }
}
</script>
