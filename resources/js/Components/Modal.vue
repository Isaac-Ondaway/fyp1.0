<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Add Event</h2>
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100">Title</label>
                    <input v-model="title" type="text" class="w-full p-2 border border-gray-300 rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100">Start Date & Time</label>
                    <input v-model="start" type="datetime-local" class="w-full p-2 border border-gray-300 rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100">End Date & Time</label>
                    <input v-model="end" type="datetime-local" class="w-full p-2 border border-gray-300 rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100">Color</label>
                    <input v-model="color" type="color" class="w-full p-2 border border-gray-300 rounded" />
                </div>
                <div class="flex justify-end space-x-4">
                    <button @click="closeModal" type="button" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isOpen: Boolean,
        startDate: String,
    },
    data() {
        return {
            title: '',
            start: this.startDate,
            end: '',
            color: '#000000'
        }
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        submitForm() {
            this.$emit('save', {
                title: this.title,
                start: this.start,
                end: this.end,
                color: this.color,
            });
            this.closeModal();
        }
    }
}
</script>

<style scoped>
.fixed {
    z-index: 50;
}
</style>
