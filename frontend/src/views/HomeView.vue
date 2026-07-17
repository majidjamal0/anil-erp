<script setup lang="ts">
const modules = ['کاربران و دسترسی‌ها','شعب و انبارها','محصولات و موجودی','مشتریان و فروش','مالی','منابع انسانی','تولید','گزارش‌ها']
</script>
<template><section><p class="eyebrow">زیرساخت یکپارچه کسب‌وکار</p><h1>سامانه مدیریت منابع سازمانی حجاب آنیل</h1><p>پایه‌ای امن و توسعه‌پذیر برای مدیریت عملیات روزانه برند.</p><div class="grid"><article v-for="module in modules" :key="module">{{ module }}</article></div></section></template>
