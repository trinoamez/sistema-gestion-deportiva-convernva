# ===== SISTEMA DE GESTIÓN DEPORTIVA CONVERNVA =====
# Makefile para automatización de tareas de desarrollo

.PHONY: help install test lint stan clean deploy backup

# Variables
PHP := php
COMPOSER := composer
PHPUNIT := vendor/bin/phpunit
PHPCS := vendor/bin/phpcs
PHPCBF := vendor/bin/phpcbf
PHPSTAN := vendor/bin/phpstan
NODE := node
NPM := npm

# Colores para output
GREEN := \033[0;32m
YELLOW := \033[1;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Mostrar ayuda
	@echo "$(GREEN)Sistema de Gestión Deportiva Convernva$(NC)"
	@echo "$(YELLOW)Comandos disponibles:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'

install: ## Instalar dependencias del proyecto
	@echo "$(GREEN)Instalando dependencias...$(NC)"
	$(COMPOSER) install --no-dev --optimize-autoloader
	@echo "$(GREEN)✓ Dependencias instaladas$(NC)"

install-dev: ## Instalar dependencias de desarrollo
	@echo "$(GREEN)Instalando dependencias de desarrollo...$(NC)"
	$(COMPOSER) install
	@echo "$(GREEN)✓ Dependencias de desarrollo instaladas$(NC)"

test: ## Ejecutar tests
	@echo "$(GREEN)Ejecutando tests...$(NC)"
	$(PHPUNIT) --colors=always
	@echo "$(GREEN)✓ Tests completados$(NC)"

test-coverage: ## Ejecutar tests con cobertura
	@echo "$(GREEN)Ejecutando tests con cobertura...$(NC)"
	$(PHPUNIT) --coverage-html coverage --colors=always
	@echo "$(GREEN)✓ Reporte de cobertura generado en coverage/$(NC)"

lint: ## Verificar estándares de código
	@echo "$(GREEN)Verificando estándares de código...$(NC)"
	$(PHPCS) --standard=phpcs.xml --colors
	@echo "$(GREEN)✓ Verificación de estándares completada$(NC)"

lint-fix: ## Corregir automáticamente estándares de código
	@echo "$(GREEN)Corrigiendo estándares de código...$(NC)"
	$(PHPCBF) --standard=phpcs.xml
	@echo "$(GREEN)✓ Corrección automática completada$(NC)"

stan: ## Análisis estático con PHPStan
	@echo "$(GREEN)Ejecutando análisis estático...$(NC)"
	$(PHPSTAN) analyse --configuration=phpstan.neon
	@echo "$(GREEN)✓ Análisis estático completado$(NC)"

quality: lint stan ## Verificar calidad del código completo
	@echo "$(GREEN)✓ Verificación de calidad completada$(NC)"

clean: ## Limpiar archivos temporales
	@echo "$(GREEN)Limpiando archivos temporales...$(NC)"
	rm -rf coverage/
	rm -rf .phpunit.cache/
	rm -rf .phpstan.cache/
	rm -rf vendor/
	rm -rf node_modules/
	@echo "$(GREEN)✓ Limpieza completada$(NC)"

backup: ## Crear backup de la base de datos
	@echo "$(GREEN)Creando backup de la base de datos...$(NC)"
	@mkdir -p backups
	@mysqldump -u root -p convernva > backups/convernva_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✓ Backup creado en backups/$(NC)"

deploy: ## Preparar para deployment
	@echo "$(GREEN)Preparando para deployment...$(NC)"
	$(COMPOSER) install --no-dev --optimize-autoloader
	$(MAKE) quality
	$(MAKE) test
	@echo "$(GREEN)✓ Proyecto listo para deployment$(NC)"

setup: install-dev ## Configuración inicial del proyecto
	@echo "$(GREEN)Configuración inicial completada$(NC)"
	@echo "$(YELLOW)Recuerda:$(NC)"
	@echo "  - Copiar env.example a .env"
	@echo "  - Configurar la base de datos"
	@echo "  - Ejecutar 'make test' para verificar la instalación"

# Comandos de desarrollo rápido
dev: install-dev quality test ## Desarrollo completo
	@echo "$(GREEN)✓ Entorno de desarrollo configurado$(NC)"

quick-test: ## Test rápido sin dependencias
	@echo "$(GREEN)Ejecutando test rápido...$(NC)"
	$(PHP) -l index.php
	@echo "$(GREEN)✓ Sintaxis PHP verificada$(NC)"


