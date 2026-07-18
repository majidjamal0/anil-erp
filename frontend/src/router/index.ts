import { createRouter, createWebHistory } from 'vue-router'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import { useAuthStore } from '../stores/auth'
import DashboardView from '../views/DashboardView.vue'
import ErrorView from '../views/ErrorView.vue'
import ForgotPasswordView from '../views/ForgotPasswordView.vue'
import LoginView from '../views/LoginView.vue'
import ResetPasswordView from '../views/ResetPasswordView.vue'
import ResourceView from '../views/admin/ResourceView.vue'
import OrganizationAccessView from '../views/admin/OrganizationAccessView.vue'
import OrganizationView from '../views/admin/OrganizationView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: LoginView, meta: { guest: true } },
    { path: '/forgot-password', component: ForgotPasswordView, meta: { guest: true } },
    { path: '/reset-password', component: ResetPasswordView, meta: { guest: true } },
    {
      path: '/',
      component: DashboardLayout,
      meta: { auth: true },
      children: [
        { path: '', component: DashboardView },

        {
          path: 'organization/companies',
          component: OrganizationView,
          props: { resource: 'companies', title: 'شرکت‌ها', permission: 'companies.view' },
          meta: { permission: 'companies.view' },
        },
        {
          path: 'organization/branches',
          component: OrganizationView,
          props: { resource: 'branches', title: 'شعب و واحدها', permission: 'branches.view' },
          meta: { permission: 'branches.view' },
        },
        {
          path: 'organization/warehouses',
          component: OrganizationView,
          props: { resource: 'warehouses', title: 'انبارها', permission: 'warehouses.view' },
          meta: { permission: 'warehouses.view' },
        },
        {
          path: 'organization/warehouse-types',
          component: OrganizationView,
          props: {
            resource: 'warehouse-types',
            title: 'انواع انبار',
            permission: 'warehouse_types.manage',
          },
          meta: { permission: 'warehouse_types.manage' },
        },
        {
          path: 'organization/sales-channels',
          component: OrganizationView,
          props: {
            resource: 'sales-channels',
            title: 'کانال‌های فروش',
            permission: 'sales_channels.view',
          },
          meta: { permission: 'sales_channels.view' },
        },
        {
          path: 'organization/access',
          component: OrganizationAccessView,
          meta: { permission: 'organization.assign_access' },
        },
        {
          path: 'users',
          component: ResourceView,
          props: { resource: 'users', title: 'مدیریت کاربران', canEdit: true },
          meta: { permission: 'users.view' },
        },
        {
          path: 'roles',
          component: ResourceView,
          props: { resource: 'roles', title: 'مدیریت نقش‌ها', canEdit: true },
          meta: { permission: 'roles.manage' },
        },
        {
          path: 'permissions',
          component: ResourceView,
          props: { resource: 'permissions', title: 'مدیریت مجوزها', canEdit: true },
          meta: { permission: 'permissions.manage' },
        },
      ],
    },
    {
      path: '/unauthorized',
      component: ErrorView,
      props: { code: '403', message: 'شما اجازه دسترسی به این بخش را ندارید.' },
    },
    { path: '/:pathMatch(.*)*', component: ErrorView },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (!auth.initialized) await auth.fetchUser()
  if (to.meta.auth && !auth.authenticated) return '/login'
  if (to.meta.guest && auth.authenticated) return '/'
  if (to.meta.permission && !auth.can(String(to.meta.permission))) return '/unauthorized'
})

export default router
