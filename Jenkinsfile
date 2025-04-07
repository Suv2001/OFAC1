pipeline {
    agent any

    environment {
        DEPLOY_DIR = "/var/www/html/ofac"
        REPO_URL = "https://github.com/Suv2001/OFAC1.git"
    }

    stages {
        stage('Clean Package Locks (if any)') {
            steps {
                echo "Cleaning possible broken apt locks..."
                sh '''
                    sudo rm -f /var/lib/dpkg/lock-frontend /var/lib/dpkg/lock
                '''
            }
        }

        stage('System Update') {
            steps {
                echo "Updating system packages..."
                sh '''
                    sudo DEBIAN_FRONTEND=noninteractive apt update -y
                    sudo DEBIAN_FRONTEND=noninteractive apt upgrade -yq
                '''
            }
        }

        stage('Install Apache, PHP, MySQL') {
            steps {
                echo "Installing Apache, PHP, and MySQL..."
                sh '''
                    sudo DEBIAN_FRONTEND=noninteractive apt install apache2 -y
                    sudo DEBIAN_FRONTEND=noninteractive apt install php libapache2-mod-php php-mysql -y
                    sudo DEBIAN_FRONTEND=noninteractive apt install mysql-server -y
                '''
            }
        }

        stage('Start and Enable Services') {
            steps {
                echo "Starting Apache and MySQL..."
                sh '''
                    sudo systemctl start apache2
                    sudo systemctl enable apache2

                    sudo systemctl start mysql
                    sudo systemctl enable mysql
                '''
            }
        }

        stage('Clone Repository') {
            steps {
                echo "Cloning OFAC1 repository..."
                sh "rm -rf OFAC1"
                sh "git clone ${REPO_URL}"
            }
        }

        stage('Deploy to Apache Directory') {
            steps {
                echo "Deploying project to ${DEPLOY_DIR}..."
                sh '''
                    sudo mkdir -p ${DEPLOY_DIR}
                    sudo rm -rf ${DEPLOY_DIR}/*
                    sudo cp -r OFAC1/* ${DEPLOY_DIR}/
                    sudo chown -R www-data:www-data ${DEPLOY_DIR}
                    sudo chmod -R 755 ${DEPLOY_DIR}
                '''
            }
        }
    }

    post {
        success {
            echo "✅ LAMP stack installed and project deployed successfully!"
        }
        failure {
            echo "❌ Something went wrong during the setup or deployment."
        }
    }
}
