# Installation

- Create config file **.env** using **.env.example**

| Key(s) | Value |
| --- | --- |
| APP_URL | App base URL |
| DB_ | MySQL connection credentials |
| DB2_ | Old login module MySQL connection credentials |
| MAIL_ | Mail driver config |
| FACEBOOK_ | FB oAuth credentials |
| GOOGLE_ | Google oAuth credentials |
| PMS_ | PMS oAuth credentials |
| AUTH_MASTER_HASH | admin master password hash, use bcrypt function to create new one |

- Run **install.sh**

# Maintenance

- Import accounts from old login module database
``` php artisan lm:import ```

- Merge two login-module databases
``` php artisan lm:merge HOST PORT DATABASE USERNAME PASSWORD ```

- Import accounts from bebras database
``` php artisan bebras:import HOST PORT DATABASE USERNAME PASSWORD ```