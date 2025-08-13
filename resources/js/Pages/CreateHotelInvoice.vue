<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {usePage, Link, useForm, router} from "@inertiajs/vue3";
import {computed, ref} from 'vue';
import Swal from "sweetalert2";
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
dayjs.extend(customParseFormat);
const userData = ref(usePage().props.reservations);
const tableHeaders = [
    { text: 'Booking No', value: 'reservation_no' },
    { text: 'C/IN', value: 'check_in' },
    { text: 'C/OUT', value: 'check_out' },
    { text: 'Name', value: 'guest_name' },
    { text: 'Hotel', value: 'hotel.hotelName' },
    { text: 'Total Price',  value: 'total_price_bdt' },
    { text: 'Status',  value: 'reservation_status.status' },
    { text: 'Actions', value: 'actions' },
];
function getTotalPriceInBDT(rooms, rate) {
    let total = 0;
    rooms.forEach(room => {
        const price = parseFloat(room.total_price) || 0;
        const currency = room.currency?.currency;

        if (currency === 'USD') {
            total += price * parseFloat(rate || 0);
        } else {
            total += price;
        }
    });
    return total.toFixed(2);
}
const searchValue = ref('');
const processedUserData = computed(() =>
    userData.value.map((item) => ({
        ...item,
        hotelName: item.hotel?.hotelName ?? '',
    }))
);
const sortedUserData = computed(() => {
    return [...processedUserData.value].sort((a, b) => {
        return new Date(a.check_in) - new Date(b.check_in);
    });
});
const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    const day = date.getDate();
    const month = date.toLocaleString('en-GB', { month: 'long' }).toLowerCase();
    const year = date.getFullYear();
    return `${day} ${month} ${year}`;
};
const reservationData = useForm({
    reservation_id: '',
    hotel_id: '',
    total_amount: '',
    total_advance: null,
    advanceCurency: null,
    exchange_rate: '',
    commission_type:'',
    commission_value:null,
    rooms: []
})
const fetchUsers = () => {
    router.reload({
        only: ['reservations'],
        onSuccess: () => {
            userData.value = usePage().props.reservations ;
        }
    });
};
const handleCreate=(item)=>{
    reservationData.reservation_id =  item.id;
    reservationData.hotel_id = item.hotel.id;
    reservationData.total_amount = getTotalPriceInBDT(item.rooms,item.rate.rate);
    reservationData.total_advance = item.total_advance;
    reservationData.advanceCurency = item.currency?.id ? item.currency.id : null;
    reservationData.exchange_rate = item.rate.rate;
    reservationData.commission_type = item.hotel.commissionType;
    if(item.hotel.commissionType === "percent" && item.payment_method.payment === "Hotel Collects"){
        reservationData.commission_value = item.hotel.hotelCollectsCommission;
    }
    else if(item.hotel.commissionType === "percent" && item.payment_method.payment !== "Hotel Collects"){
        reservationData.commission_value = item.hotel.expediaCollectsCommission;
    }
    else{
        reservationData.commission_value = null;
    }
    reservationData.rooms = item.rooms.map(room => ({
        ...room,
        currency_id: room.currency?.id || null
    }));
    console.log(reservationData)
    console.log(item);
    reservationData.post('/dashboard/hotel-invoice/create-invoice', {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Invoice Created successfully',
                showConfirmButton: false,
                timer: 2000
            });
            // Reset reservation form fields
            reservationData.reset();
            fetchUsers();
        },
    });
}
</script>

<template>
    <AdminLayout>

        <div>
            <div class="flex justify-between items-center">
                <Link href="/dashboard/hotel-invoice" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>

                    Go Back
                </Link>
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/reservations/all-reservations" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        All Reservations
                    </Link>
                </div>
            </div>

            <div class="flex justify-between items-center gap-2 mb-2">
                <div class="bg-white shadow-md px-4 py-2 rounded-lg w-1/2">
                    <h1 class="text-cyan-950 text-2xl font-bold py-0.5">Create Invoice</h1>
                </div>
                <div class="relative  shadow-md w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" v-model="searchValue" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900  rounded-lg bg-white border-none focus:ring-none focus:border-none" placeholder="Search anything..." />
                </div>
            </div>
            <EasyDataTable
                buttons-pagination
                :headers="tableHeaders"
                :items="sortedUserData"
                :search-value="searchValue"
                :rows-per-page="100"
                table-class-name="customize-table"
                show-index
            >
                <template #item-check_in="item">
                    <div class="text-sm font-medium">
                        {{ formatDate(item.check_in) }}
                    </div>
                </template>

                <template #item-check_out="item">
                    <div class="text-sm font-medium">
                        {{ formatDate(item.check_out) }}
                    </div>
                </template>
                <template #item-guest_name="item">
                    <div class=" w-24 text-sm font-semibold ">
                        {{item.guest_name }}
                    </div>
                </template>
                <template #item-total_price_bdt="item">
                    <div class="text-sm font-semibold ">
                        {{ getTotalPriceInBDT(item.rooms,item.rate.rate) }} BDT
                    </div>
                </template>
                <template #expand="item">
                    <div class="p-4 grid grid-cols-4 gap-2">
                        <div>
                            <p><strong>Oboe Sl:</strong> {{ item.obeo_sl }}</p>
                            <p><strong>Booking No:</strong> {{ item.reservation_no }}</p>
                            <p><strong>Check-in:</strong> {{ item.check_in }}</p>
                            <p><strong>Check-out:</strong> {{ item.check_out }}</p>
                            <p><strong>Reservation Date:</strong> {{ item.reservation_date }}</p>

                        </div>
                        <div>
                            <p><strong>Guest Name:</strong> {{ item.guest_name }}</p>
                            <p><strong>Hotel Name:</strong> {{ item.hotel?.hotelName }}</p>
                            <p><strong>Email:</strong> {{ item.email }}</p>
                            <p><strong>Phone:</strong> {{ item.phone }}</p>
                            <p><strong>Request:</strong> {{ item.request }}</p>
                            <p><strong>Comment:</strong> {{ item.comment }}</p>

                        </div>
                        <div>
                            <strong>Rooms ({{item.rooms.length}}):</strong>
                            <ul class="list-disc ml-5">
                                <li v-for="room in item.rooms" :key="room.id">
                                    <p class="font-semibold-semibold">{{ room.name }}</p>
                                    <p>
                                        {{ room.total_room }} room(s), {{ room.total_night }} night(s), {{ room.total_price }} {{ room.currency?.currency }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <p><strong>Total Adult:</strong> {{ item.total_adult }}</p>
                            <div v-if="item.children?.length">
                                <p> <strong>Children ({{item.children.length}}):</strong > (<span v-for="child in item.children" :key="child.id"> {{ child.age }}, </span>)</p>
                            </div>
                            <p><strong>Total Advance:</strong> {{ item.total_advance }} {{ item.currency?.currency }}</p>
                            <p><strong>Exchange Rate:</strong> {{ item.rate?.rate }} Tk</p>
                            <p><strong>Payment Method:</strong>  {{ item.payment_method.payment }}</p>
                            <p><strong>Source:</strong> {{ item.source.source }}</p>
                        </div>

                    </div>
                </template>
                <template #item-actions="item">
                    <div class="flex gap-2">
                        <button @click="handleCreate(item)"  type="button" class=" text-white  rounded text-sm px-3 py-1 bg-cyan-950 hover:bg-blue-700">
                            Create
                        </button>
                        <button  type="button" class=" text-white  rounded text-sm px-3 py-1 bg-green-600 hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </template>
            </EasyDataTable>
        </div>

    </AdminLayout>
</template>

<style scoped>
::v-deep(.customize-table) {
    --easy-table-header-font-size: 16px;
    --easy-table-body-row-font-size: 14px;
    --easy-table-header-font-color: #111827;
    --easy-table-body-row-font-color: #374151;
    --easy-table-border: 1px solid #e5e7eb;
}
::v-deep(.customize-table thead th:nth-child(3)),
::v-deep(.customize-table tbody td:nth-child(3)) {
    max-width: 200px;
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
}
</style>
