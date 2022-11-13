<template>
    <Head title="Create Community" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create community
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-md mx-auto bg-white m-2 p-6">
                    <form @submit.prevent="store">
                        <div>
                            <Label for="name" value="Name" />
                            <Input 
                                type="text"
                                v-model="form.name"
                                class="block w-full mt-1"
                                autofocus
                                autocomplete="name"
                            />
                            <Error :message="form.errors.name" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <Label for="description" value="Description" />
                            <Input 
                                type="text"
                                v-model="form.description"
                                class="block w-full mt-1"
                                autofocus
                                autocomplete="description"
                            />
                            <Error :message="form.errors.description" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <Button
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Label from '@/Components/InputLabel.vue'
import Input from '@/Components/TextInput.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/PrimaryButton.vue'

const form = useForm({
    name: '',
    description: ''
})

const store = () => {
    form.post(route('communities.store'))
}
</script>