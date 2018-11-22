FROM node:10.13.0
ENV LAST_UPDATED 20181122T020500

# Copy source code
COPY . /app

# Change working directory
WORKDIR /app

# Install dependencies
RUN npm install

# Expose API port to the outside
ENV PORT 80
EXPOSE 80

# Launch application
CMD ["npm","start"]

