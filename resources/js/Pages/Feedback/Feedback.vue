<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<script>
export default {
    data: () => ({
        firstName: '',
        lastName: '',
        phone: '',
        text: '',
        formDelivered: false,
        formLoading: false,
        formError: false,
    }),

    methods: {
        async submitForm() {
            const { valid } = await this.$refs.form.validate();
            if (!valid) return;

            this.formLoading = true;
            await axios
                .post('/docs/', {
                    firstName: this.firstName,
                    lastName: this.lastName,
                    phone: this.phone,
                    text: this.text,
                })
                .catch(() => (this.formError = true));
            this.formDelivered = true;
            this.formLoading = false;
        },
    },
};
</script>

<template>
    <Head title="Feedback form" />

    <v-sheet class="mx-auto my-auto" width="600">
        <v-alert
            v-if="formError"
            color="error"
            icon="mdi-alert-circle"
            title="An error occured"
            text="Form was not sent, try again later"
            closable
        ></v-alert>

        <v-alert
            v-if="formDelivered && !formError"
            color="success"
            icon="mdi-check-circle"
            title="Success"
            text="Form has been delivered"
            closable
        ></v-alert>

        <v-form ref="form" @submit.prevent class="mt-1">
            <v-text-field
                v-model="firstName"
                label="First name"
                :rules="[(v) => !!v || 'First name is required']"
            ></v-text-field>
            <v-text-field
                v-model="lastName"
                :rules="[(v) => !!v || 'Last name is required']"
                label="Last name"
            ></v-text-field>
            <v-text-field
                v-model="phone"
                :rules="[(v) => !!v || 'Phone is required']"
                label="Phone"
            ></v-text-field>
            <v-textarea v-model="text" label="Text"></v-textarea>

            <v-btn
                class="mt-2"
                color="primary"
                type="submit"
                @click="submitForm"
                :disabled="formDelivered"
                block
                >Submit</v-btn
            >
        </v-form>
    </v-sheet>

    <v-overlay :model-value="formLoading" class="align-center justify-center">
        <v-progress-circular
            color="primary"
            size="64"
            indeterminate
        ></v-progress-circular>
    </v-overlay>
</template>
