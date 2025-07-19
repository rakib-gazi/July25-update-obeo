<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import logo from '../../images/logo.png';
import booking from '../../images/booking.png';
import obeologo from '../../images/obeologo.png';
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl mx-auto bg-gray-50 p-6 shadow-md rounded-lg">
            <!-- Header -->
            <div class=" p-4 rounded-2xl shadow-md mb-4  flex justify-between items-center bg-white">
                <img :src="obeologo" alt="Logo" class="h-16">
                <div class="text-right">
                    <div class="flex justify-end">
                        <img :src="booking" alt="Reservation Source" class="h-6">
                    </div>
                    <h1 class="text-xl font-bold text-cyan-950">Hotel Reservation</h1>
                    <h3 class="text-lg font-lg text-cyan-950">Hotel Royal Raj International</h3>
                </div>
            </div>
            <!-- Reservation information -->
            <div class="rounded-2xl bg-white shadow-md mt-2">
                <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-bold ">Reservation Information</h1>
                <div class="p-4  grid grid-cols-3  gap-2">
                    <div>
                        <p class="text-xs font-bold">Guest Name</p>
                        <h2 class="text-md font-semibold mb-1 text-black">Mohammed Rakib Gazi</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Reservation No</p>
                        <h2 class="text-md font-semibold mb-1 text-black">2229807022</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Obeo Sl</p>
                        <h2 class="text-md font-semibold mb-1 text-black">2229807022</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Check In</p>
                        <h2 class="text-md font-semibold mb-1 text-black">Jun 26, 2025</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Check Out</p>
                        <h2 class="text-md font-semibold mb-1 text-black">Jun 28, 2025</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Booked on</p>
                        <h2 class="text-md font-semibold mb-1 text-black">Jun 28, 2025</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Total Room</p>
                        <h2 class="text-md font-semibold mb-1 text-black">3 Rooms</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Total Nights</p>
                        <h2 class="text-md font-semibold mb-1 text-black">2 Nights</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Total Adults</p>
                        <h2 class="text-md font-semibold mb-1 text-black">2 Adults</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Total Children</p>
                        <h2 class="text-md font-semibold mb-1 text-black">N/A</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Phone Number</p>
                        <h2 class="text-md font-semibold mb-1 text-black">+8801810004180</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Email</p>
                        <h2 class="text-md font-semibold mb-1 text-black">webobeorooms@gmail.com</h2>
                    </div>
                </div>
            </div>
            <!--payment & Pricing  -->
            <div class="rounded-2xl bg-white shadow-md mt-2">
                <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-bold ">Payment & Pricing </h1>
                <div class="p-4  grid grid-cols-3  gap-2">
                    <div>
                        <p class="text-xs font-bold">Price (USD) </p>
                        <h2 class="text-md font-semibold mb-1 text-black">$37.00</h2>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs font-bold">Exchange Rate </p>
                        <h2 class="text-md font-semibold mb-1 text-black">122.00 TK</h2>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs font-bold">Total Price (BDT) </p>
                        <h2 class="text-md font-semibold mb-1 text-black">12200.00 TK</h2>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs font-bold">Total Advance </p>
                        <h2 class="text-md font-semibold mb-1 text-black">12200.00 TK</h2>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs font-bold">Total Pay In Hotel</p>
                        <h2 class="text-md font-semibold mb-1 text-black">12200.00 TK</h2>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs font-bold">Payment Method</p>
                        <h2 class="text-md font-semibold mb-1 text-black">Hotel Collects</h2>
                    </div>
                </div>
            </div>
            <!-- Room Wise Payment Details -->
            <div class="rounded-2xl bg-white shadow-md mt-2">
                <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-bold ">Room Wise Information & Price Details</h1>
                <div class="px-4 py-2">
                    <div class="flex justify-between">
                        <h2 class="text-md font-semibold mb-1 text-black">Deluxe Single Room (3)</h2>
                        <h2 class="text-md font-semibold mb-1 text-black"><span class="text-xs">(3 * 7000.00)</span> 21000.00 TK</h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-sm font-bold text-black">Total Night (2) </p>
                        <h2 class="text-sm font-semibold mb-1 text-black"><span class="text-xs">(2 * 14000.00)</span> 42000.00 TK</h2>
                    </div>
                    <hr class="text-black font-bold border ">
                    <div class="flex justify-between">
                        <p class="text-md font-bold">Total Amount </p>
                        <h2 class="text-md font-bold mb-1">42000.00 TK</h2>
                    </div>
                </div>
                <div class="px-4 py-2">
                    <div class="flex justify-between">
                        <h2 class="text-md font-semibold mb-1 text-black">Deluxe Single Room (3)</h2>
                        <h2 class="text-md font-semibold mb-1 text-black"><span class="text-xs">(3 * 7000.00)</span> 21000.00 TK</h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-sm font-bold text-black">Total Night (2) </p>
                        <h2 class="text-sm font-semibold mb-1 text-black"><span class="text-xs">(2 * 14000.00)</span> 42000.00 TK</h2>
                    </div>
                    <hr class="text-black font-bold border ">
                    <div class="flex justify-between">
                        <p class="text-md font-bold">Total Amount </p>
                        <h2 class="text-md font-bold mb-1">42000.00 TK</h2>
                    </div>
                </div>

            </div>
            <!-- Comments & Special Request -->
            <div class="rounded-2xl bg-white shadow-md mt-2">
                <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-bold ">Comments & Requests</h1>
                <div class="p-4 grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-xs font-bold">Special Request </p>
                        <h2 class="text-md font-semibold mb-1 text-black">Please Provide non smoking room</h2>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Comments </p>
                        <h2 class="text-md font-semibold mb-1 text-black">This guest is our vip guest</h2>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="bg-white px-4 py-8 rounded-2xl my-4 shadow-md flex items-center">
                <p class="flex-1 text-center text-Black font-semibold text-lg">&copy;  Obeo Limited.
                    All rights reserved.</p>
                <p class="text-end text-black font-semibold text-sm">Printed by: Mohammed Rakib gazi</p>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>
