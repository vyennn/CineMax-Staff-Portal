#!/bin/bash
# build.sh - Install PostgreSQL extension

echo "Installing PostgreSQL extension..."
apt-get update
apt-get install -y php-pgsql php-pdo-pgsql

echo "✅ PostgreSQL extension installed"