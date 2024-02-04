# INSTALL
### 1. docker build and up
### 2. add env in root and guestbook 
### 3. install vendors (in guestbook folder)
### 4. run migration 
php bin/console doctrine:migrations:migrate

### 5. install node modules (in guestbook folder)
### 6. run build
npm run  build


# VERSION
<table>
    <tr>
        <td>php</td>
        <td>8.2</td>
    </tr>
    <tr>
        <td>symfomy</td>
        <td>6.4</td>
    </tr>
    <tr>
        <td>node</td>
        <td>v16.20.2</td>
    </tr>
    <tr>
        <td>npm</td>
        <td>v8.19.4</td>
    </tr>
</table>
