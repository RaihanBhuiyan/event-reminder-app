FROM node:18

WORKDIR /var/www/html

# Install Vite globally
RUN npm install -g vite

COPY package*.json ./
RUN npm install
COPY . .

# Build assets (optional for dev)
# RUN npm run build

EXPOSE 5173  
# Match Vite's default port
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]