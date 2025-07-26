ENV_FILE = .env
DC = docker compose --env-file $(ENV_FILE)

APP_PORT := $(shell grep APP_PORT $(ENV_FILE) | cut -d '=' -f2)

# Dockerコマンド
up:
	$(DC) up -d --build

down:
	$(DC) down

restart: down up

# PHP（Laravel）関係のコマンド
sh-backend:
	$(DC) exec backend /bin/bash

composer-install:
	$(DC) exec backend composer install

composer-install-init:
	$(DC) run --rm backend composer install

migrate:
	$(DC) exec backend php artisan migrate

clear:
	$(DC) exec backend php artisan config:clear && \
	$(DC) exec backend php artisan cache:clear && \
	$(DC) exec backend php artisan route:clear && \
	$(DC) exec backend php artisan view:clear

# note-appコンテナのログチェック
log-app:
	$(DC) logs -f backend

# note-websocketコンテナのログチェック
log-websocket:
	$(DC) logs -f websocket

# 全削除（取扱注意）
clean:
	docker system prune -f

# まず初回実行
init: up composer-install-init migrate clear
	@echo "初期セットアップ完了 http://localhost:$(APP_PORT)"
