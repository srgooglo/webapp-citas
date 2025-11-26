Como ejecutar el proyecto:

1. Clona el repositorio:
   ```
   git clone https://github.com/BlackCherryCat/webapp-citas.git
   ```
   o descargando el codigo como zip.

2. Navega al directorio del backend:
   ```
   cd webapp-citas/packages/backend
   ```

3. Instala las dependencias:
   ```
   composer install
   ```

4. Navega al directorio del cliente:
   ```
   cd webapp-citas/packages/client
   ```

5. Instala las dependencias:
   ```
   npm install
   ```
6. Antes habra que habilitar las extensiones de php que permitan interactuar con servidores sql, descomentando las siguientes lineas del php.ini (en linux se encuentra en /etc/php/php.ini):
   ```ini
   extension=pdo_mysql
   extension=mysqli
   ```
7. Configura las variables de entorno necesarias en el archivo .env, si no no iniciara, basta con renombrar el archivo example-env a .env y completar los valores.

8. Poner en funcionamiento el servidor de SQL, para eso hay que irse a la carpeta del backend y ejecutar los siguientes comandos:
    ```
    docker compose pull
    docker compose create
    docker compose start
    ```

9. Inicia el backend, en la carpeta backend:
    ```
    composer run dev
    ```

10. Inicia el frontend, en la carpeta client:
    ```
    npm run dev
    ```
11. Para probar el pago se puede poner ```4242 4242 4242 4242```, de cvc ```123``` y de fecha de caducidad cualquier fecha futura

12. Para probar el sistema de mensaje de telegram tienes que obtener el id haciendole una peticion a userinfobot y luego poner el id a tu usuario de la webapp
