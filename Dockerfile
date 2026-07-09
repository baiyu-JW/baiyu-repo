# 使用 PHP 8.2 CLI 镜像（语法兼容 PHP 7.3）
FROM php:8.2-cli

# 工作目录
WORKDIR /app

# 复制代码与配置
COPY analyze.php /app/analyze.php
COPY src/ /app/src/
COPY config.json /app/config.json
COPY entrypoint.sh /app/entrypoint.sh

# 赋予入口脚本可执行权限
RUN chmod +x /app/entrypoint.sh

# 健康检查：确认 PHP 运行时工作正常
HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
    CMD php -r "echo 'health ok';" || exit 1

# 默认执行 php analyze.php，可通过 `docker run log-analyzer access.log` 传参
ENTRYPOINT ["/app/entrypoint.sh"]
CMD ["php", "analyze.php"]
