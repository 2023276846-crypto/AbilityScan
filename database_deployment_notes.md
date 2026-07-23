# Guide: Live Database Access, Deployment, and HeidiSQL

This guide saves our conversation regarding how the database works after you deploy your system, how to use HeidiSQL to view it, and how to prepare it for your project report.

---

## 1. Local Database vs. Live Server Database

When you deploy your system to production, you will have **two completely separate environments**:

| Environment | Where are the Web Files? | Where is the Database? |
| :--- | :--- | :--- |
| **Local (Laragon)** | On your computer | On your computer (`localhost`) |
| **Production (Live)** | On the remote server | **On the remote server** |

### Why is the live database on the server?
If the live website tried to use the database on your computer (`localhost`), your website would stop working the moment you turned off your laptop or disconnected from the Wi-Fi. Having the database on the server allows your site to run 24/7.

### How data is saved:
* When a visitor registers a new account on your live website, the data is automatically saved to the **MySQL database on the server**.
* This new user will **not** show up in your local Laragon database because they are two separate databases.

---

## 2. What is HeidiSQL?

It is important to distinguish the viewer tool from the database itself:
* **MySQL:** This is the actual database management system. It runs in the background and stores all your tables and data.
* **HeidiSQL:** This is just a **database client (viewer tool)**. It is a graphical window you use on your computer to look inside MySQL, view tables, and run SQL queries.
* *Analogy:* **MySQL** is like a website (like YouTube) hosted on a server, and **HeidiSQL** is like Google Chrome—a browser tool you use to look at it. If you close HeidiSQL, your database continues to run.

---

## 3. How to Connect HeidiSQL to your Live Server Database

Since live servers close their MySQL port (`3306`) to the public internet for security, you should connect using an **SSH Tunnel** inside HeidiSQL:

1. Open HeidiSQL and create a new session.
2. Set the **Network type** to **MariaDB or MySQL (SSH tunnel)**.
3. In the **SSH Tunnel** tab:
   * **SSH Host:** Your server's IP address.
   * **Username:** Your server SSH username (e.g., `root`, `ubuntu`).
   * **Password / Private Key:** Your server SSH password or your private key file (`.ppk` or `.pem`).
4. In the **Settings** tab:
   * **HostName/IP:** `127.0.0.1` (HeidiSQL talks locally to the tunnel).
   * **User:** Your production database username (found in the server's `.env`).
   * **Password:** Your production database password.
   * **Port:** `3306`

---

## 4. Tips for Your Project Report Screenshots

When taking screenshots of your database for your report to present to your examiner/lecturer:

* **Show the Table Structure (Schema):**
  Click on a table (e.g., `users`) in the left sidebar and screenshot the **Columns** tab. This shows your data types, primary keys, and overall table structure.
* **Show Sample Data:**
  Go to the **Data** tab at the top of HeidiSQL to display rows of sample data. This proves your system is successfully inserting and reading data.
* **Crop the Screenshots:**
  Crop your screenshots so they only show the HeidiSQL window. Hide your Windows taskbar, desktop icons, or other open browser tabs.
* **Censor Sensitive Data:**
  If you have real user records, blur or block out passwords, hashes, emails, or personal phone numbers.
