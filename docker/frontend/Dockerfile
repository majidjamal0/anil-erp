FROM node:22-alpine AS development
WORKDIR /app
COPY frontend/package.json ./
RUN npm install
COPY frontend ./
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

FROM node:22-alpine AS build
WORKDIR /app
COPY frontend/package.json ./
RUN npm install
COPY frontend ./
RUN npm run build

FROM nginx:1.27-alpine AS production
COPY docker/frontend/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
HEALTHCHECK --interval=10s --timeout=5s --retries=5 CMD wget -q -O /dev/null http://127.0.0.1:5173/
