# 野良露營 NoraCamping 總覽

## 介紹

歡迎來到**野良露營 NoraCamping**——一個結合露營愛好與動物關懷的創新平台。通過我們的網站，我們不僅提供一個親密接觸大自然的場所，還讓您有機會參與保護和照顧流浪動物的活動。我們的目標是推動一種全新的露營文化，讓休閒活動與公益行為相輔相成。

- [野良露營\_簡報](https://drive.google.com/file/d/1hTI494n8lbzKbaudzZiSmbZxcGdhtjUC/view)
- [野良露營\_展示影片](https://www.youtube.com/watch?v=9_ODuTqBn6w)
- [野良露營 Nora Camping](https://tibamef2e.com/chd104/g1/) <--正式網站請由此進入。

## 項目結構

本項目包含三個主要部分：

1. **前台**[repository](https://github.com/rillala/NORA-Camping) - 提供用戶界面，包括營地預約、線上選物等功能。
2. **後台**[repository](https://github.com/rillala/NORA-BackStage) - 給予管理者操作界面，用於管理網站內容和用戶。
3. **後端資料庫與 PHP**[repository](https://github.com/rillala/NORA-API) - 處理資料儲存和後端邏輯。

## 技術棧

- 前端開發：HTML, JavaScript, SASS/CSS, Vue 3, GSAP, Axios, VCalendar, Pinia.js
- 後端開發：PHP, MySQL, JWT, Composer
- 工具與服務：Figma, Notion, Vite, WampServer, MySQL Workbench, SourceTree, FileZilla

# 野良露營 NoraCamping 後端資料庫與 PHP

## 介紹

野良露營 NoraCamping 的後端部分主要負責處理資料儲存和後端邏輯，為前台和後台提供穩定的 API 接口。透過 WampServer 提供的 Apache 服務器、MySQL 數據庫以及 PHP 支持，結合 Composer 來管理 PHP 的包依賴，我們構建了一個穩健的後端系統。

## 開發環境設置

後端項目依賴於 WampServer 和 Composer 進行開發和包管理。請按照以下步驟設置您的開發環境：

### 必要條件

- **WampServer 3.3.2**：提供 Apache、MySQL 和 PHP 的整合環境，適用於 Windows 系統。

  - 下載並安裝 [WampServer](https://www.wampserver.com/)，選擇與您的系統架構相匹配的版本進行安裝。

- **Composer**：PHP 的依賴管理工具，用於管理後端項目的 PHP 包依賴。
  - 訪問 [Composer 官方網站](https://getcomposer.org/) 下載並安裝 Composer。

### 安裝步驟

1. **配置 WampServer**：

   - 安裝 WampServer 並啟動，確保 Apache 和 MySQL 服務運行正常。
   - 通過 WampServer 的系統托盤圖標可以訪問 PHPMyAdmin，用於管理 MySQL 數據庫。

2. **安裝 Composer 依賴**：
   - 在後端項目根目錄下，打開命令行或終端，執行以下命令來安裝 PHP 包依賴：
     ```bash
     composer install
     ```
   - 這會根據 `composer.json` 文件安裝所有必需的 PHP 包，包括 `firebase/php-jwt` 用於 JWT 認證，`phpmailer/phpmailer` 用於郵件處理。

## 功能特色

- **JWT 加密登入**：採用 `firebase/php-jwt` 實現安全的用戶認證機制，保障用戶登入過程的安全性。
- **資料庫管理**：透過 WampServer 的 MySQL 服務和 PHPMyAdmin 進行資料庫的設計和管理。
- **API 開發**：基於 PHP 開發穩定的 RESTful API，支持前台和後台之間的數據交互。
