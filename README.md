# 日志分析工具（AI 辅助开发考核）

## 项目简介
本项目是华傲数据 AI 辅助开发考核的产物，是一个支持多种日志格式的命令行日志分析工具，由 PHP 编写，全程使用 **Kilo Code** AI 编程助手辅助开发。

该工具能够读取 Nginx / Apache / JSONL 格式的日志文件，自动统计总请求数、状态码分布、独立 IP 数、每小时请求趋势，并生成可视化 HTML 报告。

## 功能列表
- ✅ 支持 Nginx / Apache / JSONL 三种日志格式（通过 `config.json` 配置切换）
- ✅ 统计总请求数、独立 IP 数、状态码分布
- ✅ 支持 Top N IP 排名（可配置）
- ✅ 输出可视化 HTML 报告（深色科技风 + ECharts 图表）
- ✅ 已完成单元测试（21 个测试全部通过）
- ✅ 支持 Docker 容器化一键运行

## 技术栈
- PHP 7.3+
- Composer（依赖管理）
- ECharts 5（可视化图表）
- PHPUnit 9.6（单元测试）
- Docker（容器化部署）
- Git（版本管理）

## 目录结构
baiyu-repo/
├── analyze.php # 主入口文件
├── config.json # 配置文件（log_type / log_file / top_n）
├── entrypoint.sh # Docker 入口脚本
├── Dockerfile # Docker 镜像构建文件
├── .dockerignore # Docker 构建排除规则
├── src/
│ ├── LogParserInterface.php # 解析器接口
│ ├── NginxParser.php # Nginx 日志解析器
│ ├── ApacheParser.php # Apache 日志解析器
│ ├── JsonlParser.php # JSONL 日志解析器
│ └── LogParserFactory.php # 解析器工厂
├── tests/ # PHPUnit 单元测试
│ ├── bootstrap.php
│ ├── NginxParserTest.php
│ ├── ApacheParserTest.php
│ └── JsonlParserTest.php
├── report.html # 可视化报告（V3 生成）
├── phpunit.xml.dist # PHPUnit 配置文件
├── AI开发考核_唐锦玉_日志分析工具.jsonl # AI 交互记录（5 轮完整）
└── README.md # 项目说明

text

## 如何运行

用户只需将自己的日志文件放入项目根目录，运行以下命令即可自动生成可视化报告。

### 方式一：直接运行（需 PHP 7.3+）
```bash
php analyze.php
方式二：Docker 运行（无需安装 PHP）
bash
# 构建镜像
docker build -t log-analyzer .

# 默认运行（使用镜像内 config.json 的 log_file）
docker run --rm log-analyzer

# 挂载并指定日志文件分析
docker run --rm -v "$(pwd)/access.log:/app/access.log" log-analyzer access.log

# 分析其他格式的日志文件（如 JSONL）
docker run --rm -v "$(pwd)/app.jsonl:/app/app.jsonl" log-analyzer app.jsonl
配置文件说明（config.json）
json
{
    "log_type": "nginx",   // 可选: nginx / apache / jsonl
    "log_file": "access.log",
    "top_n": 10
}
开发过程
本项目采用 AI 辅助开发 模式，全程使用 Kilo Code 编程智能体完成代码生成与重构。完整交互记录见 AI开发考核_唐锦玉_日志分析工具.jsonl。

开发迭代说明
版本	内容
V1	基础功能：Nginx 日志分析，命令行输出表格
V2	架构升级：引入工厂模式，支持 Nginx / Apache / JSONL 三种格式
V3	可视化报告：生成深色科技风 HTML 报告（ECharts + 交互图表）
V4	单元测试：21 个测试用例全部通过，覆盖正常/异常输入
V5	容器化部署：Dockerfile + entrypoint + HEALTHCHECK
Git 提交历史
版本	说明	commit hash
V1	AI生成基础日志分析工具	51f84d2
V1	添加JSONL交互记录	1d32b9f
V2	支持多日志格式（Nginx/Apache/JSONL），引入工厂模式	53e82a5
V2	追加JSONL交互记录	45693af
V3	生成可视化报告（ECharts + 深色科技风）	446494c
V3	追加JSONL交互记录	1612614
V4	增加单元测试（21个测试全部通过）	ddb6379
V4	追加JSONL交互记录	92aee87
V5	添加 Docker 容器化部署（entrypoint + HEALTHCHECK）	2f0be4e
V5	整理JSONL格式（5条记录完整）	32ebfbe
作者
唐锦玉（深圳职业技术大学 · 人工智能学院 · 大数据技术专业）

推荐人：管明雷老师

日期：2026年7月

备注
本项目所有代码均由 Kilo Code AI 编程助手辅助生成，本人负责需求拆解、代码审核与功能测试。

完整 AI 交互记录见 AI开发考核_唐锦玉_日志分析工具.jsonl。

项目地址：https://github.com/baiyu-JW/baiyu-repo