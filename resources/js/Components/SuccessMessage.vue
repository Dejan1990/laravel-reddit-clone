<template>
    <div v-if="$page.props.flash.message" class="bg-green-400">
        <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                    <p class="ml-3 font-medium text-white truncate">
                    <span class="md:hidden"> {{ $page.props.flash.message }} </span>
                    <span class="hidden md:inline">
                        {{ $page.props.flash.message }}
                    </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>