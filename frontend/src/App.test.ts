import { mount } from '@vue/test-utils'; import { createPinia } from 'pinia'; import App from './App.vue';
it('renders the product name',()=>{expect(mount(App,{global:{plugins:[createPinia()],stubs:['RouterView']}}).text()).toContain('انیل ERP')})
