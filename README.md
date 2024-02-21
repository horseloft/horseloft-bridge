# 说明
当前项目是Isobaric-phoenix框架的基础组件

## v2.0.0
1. 

## v1.2.1
1. Request::all()、Request::get()、方法新增上传文件支持

## v1.2.0
1. 重构路由配置方式
2. 优化异常处理
3. 优化拦截器

## v1.1.3
1. 重命名日志类为Log
2. 新增Log静态方法log()用于自定义日志记录

## v1.1.2
1. 优化全局异常和错误处理：不再主动记录程序错误
2. 移除error_trace_log配置项
3. 移除error_log配置项

## v1.1.1
1. 修复定时任务时间解析的错误
2. 优化crontab、command命令的代码组织结构

## v1.1.0
1. 新增command命令，用于执行命令行脚本
2. 新增crontab命令，用于执行定时脚本
3. 区分CLI和非CLI响应输出

## v1.0.4
1. 修复非info日志文件名称错误问题
2. 修复默认响应头Content-Type未被引用的错误
3. 优化异常和错误的处理

## v1.0.3
1. 新增日志记录内容可配置功能
2. 优化请求路由不存在时的响应
3. 新增异常类用于处理不用场景下的异常信息
4. 优化路由匹配
5. 优化基础配置参数命名格式
6. 修改容器类属性和方法命名
7. 新增日志切割存储

## v1.0.2
1. 优化cors支持多个域名的跨域配置

## v1.0.1
1. 新增辅助函数
2. 优化拦截器命名
3. 优化配置文件加载
4. 重命名基础组件类文件
5. 优化路由和路由组件验证

# version
1. 第一位数字为主版本号，版本变更不向下兼容；用于整体功能升级和框架结构升级
2. 第二位数字为子版本号，版本变更向下兼容；用于新增功能
3. 第三位数字为修订版本号，版本变更向下兼容；用于BUG修复
4. v1版本适用于: Isobaric/phoenix >= 1.0
5. v2版本适用于：Isobaric/phoenix >= 2.0
