<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
defineOptions({ layout: UserLayout })
import logo from "../../images/logo.png"
import {router, useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
const error = ref("");
const data = useForm(
    {
        'email' : "",
        'password' : "",
    }
)
const login = ()=>{
    error.value = "";
    data.post('login',{
        onError: (errors) => {
            if (errors.message) {
                error.value = errors.message;
            }
        }
    })
}
</script>

<template>
    <UserLayout>
        <div class="h-screen w-screen bg-[#132749] flex justify-center items-center ">
            <div class="bg-cyan-950 p-6 rounded-xl shadow max-w-96 md:max-w-xl w-full font-nunito">
                <div class="pt-4">
                    <div class="flex justify-center items-center">
                        <img :src="logo" alt="logo" class="w-1/4">
                    </div>
                    <h1 class="text-2xl font-medium text-center py-4 text-white mt-4">Admin Login</h1>
                </div>
                <form class="w-full " @submit.prevent="login">
                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block mb-2 font-medium text-white">Email</label>
                        <input type="email" id="email" v-model="data.email"
                               class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                        />
                        <div v-if="data.errors.email" class="text-red-500 text-sm mt-1">{{ data.errors.email }}</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-white">Password</label>
                        <input type="password" v-model="data.password" id="password"
                               class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                               autocomplete="new-password" />
                        <div v-if="data.errors.password" class="text-red-500 text-sm mt-1">{{ data.errors.password }}</div>
                        <div v-else class="text-red-500 text-sm mt-1">
                            {{ error }}
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            :disabled="data.processing"
                            class="flex items-center justify-center gap-2 text-white bg-[#10284a] hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 w-full disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="data.processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        <span>{{ data.processing ? 'Logging in...' : 'LogIn' }}</span>
                    </button>

                </form>
            </div>
        </div>
    </UserLayout>

</template>
