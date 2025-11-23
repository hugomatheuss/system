#!/usr/bin/env bash
set -euo pipefail

# docker-entrypoint.sh
# Ajusta permissões úteis para projetos Laravel quando o código é montado como volume.
# Ativado por padrão. Para desativar, defina FIX_PERMISSIONS=0 no seu docker-compose.

FIX_PERMISSIONS=${FIX_PERMISSIONS:-1}
WWWUSER=${WWWUSER:-www-data}
WWWGROUP=${WWWGROUP:-www-data}

resolve_uid() {
  local user="$1"
  if [[ "$user" =~ ^[0-9]+$ ]]; then
    echo "$user"
    return 0
  fi
  if id -u "$user" >/dev/null 2>&1; then
    id -u "$user"
    return 0
  fi
  # fallback to numeric uid of www-data if available
  id -u www-data 2>/dev/null || echo "0"
}

resolve_gid() {
  local group="$1"
  if [[ "$group" =~ ^[0-9]+$ ]]; then
    echo "$group"
    return 0
  fi
  if getent group "$group" >/dev/null 2>&1; then
    getent group "$group" | cut -d: -f3
    return 0
  fi
  # fallback to gid of www-data if available
  id -g www-data 2>/dev/null || echo "0"
}

ensure_dir() {
  if [ -d "$1" ]; then
    return 0
  fi
  return 1
}

if [ "$FIX_PERMISSIONS" != "0" ]; then
  echo "[entrypoint] Verificando/ajustando permissões (target=${WWWUSER}:${WWWGROUP})..."

  TARGET_UID=$(resolve_uid "$WWWUSER")
  TARGET_GID=$(resolve_gid "$WWWGROUP")
  TARGET_OWNER="${TARGET_UID}:${TARGET_GID}"

  DIRS=(/var/www/storage /var/www/bootstrap/cache)

  for d in "${DIRS[@]}"; do
    if ensure_dir "$d"; then
      CUR_OWNER=$(stat -c '%u:%g' "$d" || echo "")
      if [ "$CUR_OWNER" = "$TARGET_OWNER" ]; then
        echo "[entrypoint] $d já tem owner ${TARGET_OWNER}, pulando chown"
      else
        echo "[entrypoint] $d owner atual=${CUR_OWNER:-unknown}, alvo=${TARGET_OWNER} -> ajustando"
        if [ "$(id -u)" -eq 0 ]; then
          chown -R ${TARGET_UID}:${TARGET_GID} "$d" || echo "[entrypoint] chown falhou para $d"
          find "$d" -type d -exec chmod 0755 {} + || true
          find "$d" -type f -exec chmod 0644 {} + || true
        else
          echo "[entrypoint] não é root, não é possível chown; aplicando fallback chmod para garantir escrita"
          chmod -R ug+rwX,o+rwX "$d" || echo "[entrypoint] chmod falhou para $d"
        fi
      fi
    else
      echo "[entrypoint] Diretorio $d nao existe, pulando"
    fi
  done

else
  echo "[entrypoint] FIX_PERMISSIONS=0, pulando ajuste de permissões"
fi

# Se nenhum comando for passado, iniciar php-fpm
if [ "$#" -eq 0 ]; then
  exec php-fpm
else
  exec "$@"
fi
