<template>
  <Head title="Edit Community" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Post for {{ community.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto bg-white m-2 p-6">
          <form @submit.prevent="update">
            <div>
              <Label for="title" value="Title" />
              <Input
                id="title"
                type="text"
                class="mt-1 block w-full"
                v-model="form.title"
                autofocus
                autocomplete="title"
              />
              <InputError :message="form.errors.title" />
            </div>

            <div class="mt-4">
              <Label for="url" value="Url" />
              <Input
                id="url"
                type="url"
                class="mt-1 block w-full"
                v-model="form.url"
                autocomplete="url"
              />
              <InputError :message="form.errors.url" />
            </div>

            <div class="mt-4">
              <Label for="description" value="Description" />
              <Input
                id="description"
                type="text"
                class="mt-1 block w-full"
                v-model="form.description"
                autocomplete="description"
              />
              <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <Button
                class="ml-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Update
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/inertia-vue3'
import Label from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import Input from '@/Components/TextInput.vue'
import Button from '@/Components/PrimaryButton.vue'

const props = defineProps({
    community: Object,
    post: Object
})

const form = useForm({
    title: props.post?.title,
    description: props.post?.description,
    url: props.post?.url,
})

const update = () => {
    form.put(route('communities.posts.update', [
        props.community.slug, 
        props.post.slug
    ]))
}
</script>