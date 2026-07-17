import { defineConfig } from 'vite'; import vue from '@vitejs/plugin-vue';
export default defineConfig({plugins:[vue()],server:{proxy:{'/api':'http://backend:9000'}},test:{environment:'jsdom',globals:true}})
