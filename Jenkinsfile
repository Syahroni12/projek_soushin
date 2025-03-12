pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build') {
            steps {
                script {
                    docker.image('shippingdocker/php-composer:7.4').inside('-u root') {
                        sh 'rm -f composer.lock'  // -f supaya kalau ga ada file, ga error
                        sh 'composer install'
                    }
                }
            }
        }

        stage('Testing') {
            steps {
                script {
                    docker.image('ubuntu').inside('-u root') {
                        sh 'echo "Ini adalah test"'
                    }
                }
            }
        }
    }

    post {
        success {
            echo 'Pipeline sukses!'
        }
        failure {
            echo 'Pipeline gagal, cek log untuk detailnya.'
        }
    }
} 

// Pastikan Docker aktif dan Jenkins punya akses ke Docker socket
// Kalau Jenkins jalan di Docker, pastikan Docker in Docker (DinD) aktif ya!
