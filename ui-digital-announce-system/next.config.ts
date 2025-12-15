import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* config options here */
  reactStrictMode: true,
  // Use webpack with polling for Docker compatibility
  webpack: (config, { isServer }) => {
    // Enable polling for file changes in Docker
    config.watchOptions = {
      poll: 1000, // Check for changes every second
      aggregateTimeout: 300, // Delay rebuild after first change
    };
    return config;
  },
};

export default nextConfig;
