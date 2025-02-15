<template>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center my-4">
            <h1 class="display-4">📅 Event Reminder</h1>
            <div v-if="isOffline" class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="me-2">⚠️</span>
                <span>You are currently offline. Changes will be saved locally.</span>
            </div>
        </header>

        <main>
            <section class="card p-4 mb-4">
                <h2 class="h4 mb-4">➕ Create New Event</h2>
                <form @submit.prevent="addEvent">
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input v-model="newEvent.title" class="form-control" placeholder="Team Meeting" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea v-model="newEvent.description" class="form-control" rows="2"
                            placeholder="Add details..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Date & Time</label>
                            <input v-model="newEvent.reminder_time" type="datetime-local" class="form-control"
                                required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Recipients (comma separated)</label>
                            <input v-model="newEvent.recipients" class="form-control"
                                placeholder="email1@example.com, email2@example.com" required />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Event</button>
                </form>
            </section>

            <div class="mb-4">
                <section class="card p-4 mb-4">
                    <h3 class="h5 mb-4">📤 Import Events</h3>
                    <div class="d-flex align-items-center">
                        <input type="file" ref="csvFile" accept=".csv" class="form-control me-2" />
                        <button @click="handleFileUpload" class="btn btn-secondary me-2">Upload CSV</button>
                        <button @click="downloadDemoFile" class="btn btn-primary me-2">Download
                            Template</button>
                    </div>
                </section>
            </div>

            <section class="card p-4 mb-4">
                <h3 class="h5 mb-4">📅 Upcoming Events ({{ upcomingEvents.length }})</h3>
                <div v-for="event in upcomingEvents" :key="event.id" class="card mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="h5">{{ event.title }}</h3>
                        <span class="text-muted">{{ formatDate(event.reminder_time) }}</span>
                    </div>
                    <p class="text-muted">{{ event.description || 'No description' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-primary">
                            📧 {{ event.recipients.join(', ') }}
                        </div>
                        <div class="text-muted">
                            🆔 {{ event.reminder_id }}
                        </div>
                        <button @click="markAsCompleted(event.id)" class="btn btn-success">✅ Mark Complete</button>
                    </div>
                </div>
            </section>

            <section class="card p-4 mb-4">
                <h3 class="h5 mb-4">✔️ Completed Events ({{ completedEvents.length }})</h3>
                <div v-for="event in completedEvents" :key="event.id" class="card mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="h5">{{ event.title }}</h3>
                        <span class="text-muted">{{ formatDate(event.reminder_time) }}</span>
                    </div>
                    <p class="text-muted">{{ event.description || 'No description' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-primary">
                            📧 {{ event.recipients.join(', ') }}
                        </div>
                        <div class="text-muted">
                            🆔 {{ event.reminder_id }}
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            events: [], // All events
            newEvent: {
                title: '',
                description: '',
                reminder_time: '',
                recipients: '',
            },
            isOffline: !navigator.onLine, // Track online/offline status
        };
    },
    computed: {
        // Filter upcoming events
        upcomingEvents() {
            return this.events.filter(
                (event) => !event.is_completed && new Date(event.reminder_time) > new Date()
            );
        },
        // Filter completed events
        completedEvents() {
            return this.events.filter(
                (event) => event.is_completed || new Date(event.reminder_time) <= new Date()
            );
        },
    },
    methods: {
        // Format date for display
        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        },

        // Fetch all events from the backend
        async fetchEvents() {
            try {
                const response = await axios.get('/api/events');
                this.events = response.data;

                // Sync offline events if any
                if (navigator.onLine) {
                    await this.syncOfflineEvents();
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        },

        // Add a new event
        async addEvent() {
            const eventData = {
                ...this.newEvent,
                recipients: this.newEvent.recipients.split(','), // Convert string to array
                is_completed: false,
            };

            try {
                if (navigator.onLine) {
                    const response = await axios.post('/api/events', eventData);
                    this.events.push(response.data);
                } else {
                    // Save to localStorage when offline
                    this.saveEventOffline(eventData);
                    this.events.push(eventData); // Add to local state
                }

                this.resetForm();
            } catch (error) {
                console.error('Error adding event:', error);
            }
        },

        // Mark an event as completed
        async markAsCompleted(eventId) {
            try {
                if (navigator.onLine) {
                    await axios.put(`/api/events/${eventId}`, { is_completed: true });
                    this.fetchEvents(); // Refresh the list
                } else {
                    // Mark as completed locally
                    const event = this.events.find((e) => e.id === eventId);
                    if (event) {
                        event.is_completed = true;
                    }
                }
            } catch (error) {
                console.error('Error marking event complete:', error);
            }
        },

        // Handle CSV file upload
        async handleFileUpload() {
            const file = this.$refs.csvFile.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                await axios.post('/api/events/import', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                this.fetchEvents(); // Refresh the event list
                alert('Events imported successfully!');
            } catch (error) {
                console.error('Error importing CSV:', error);
                alert('Failed to import events. Please check the file format.');
            }
        },

        // Download Demo CSV
        downloadDemoFile() {
            const link = document.createElement('a');
            link.href = '/event_reminders.csv'; // Path to the file in the public folder
            link.setAttribute('download', 'demo_events.csv'); // Force download with this name
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        // Save event to localStorage when offline
        saveEventOffline(event) {
            const offlineEvents = JSON.parse(localStorage.getItem('offlineEvents')) || [];
            offlineEvents.push(event);
            localStorage.setItem('offlineEvents', JSON.stringify(offlineEvents));
        },

        // Sync offline events when online
        async syncOfflineEvents() {
            const offlineEvents = JSON.parse(localStorage.getItem('offlineEvents')) || [];
            if (offlineEvents.length > 0) {
                try {
                    await Promise.all(
                        offlineEvents.map((event) => axios.post('/api/events', event))
                    );
                    localStorage.removeItem('offlineEvents');
                    console.log('Offline events synced successfully');
                } catch (error) {
                    console.error('Error syncing offline events:', error);
                }
            }
        },

        // Reset form after submission
        resetForm() {
            this.newEvent = {
                title: '',
                description: '',
                reminder_time: '',
                recipients: '',
            };
        },
    },
    mounted() {
        this.fetchEvents(); // Load events when the component is mounted

        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.isOffline = false;
            this.syncOfflineEvents();
        });
        window.addEventListener('offline', () => {
            this.isOffline = true;
        });
    },
};
</script>

<style scoped>
/* Add any additional custom styles here if needed */
</style>
