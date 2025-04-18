pipeline {
    agent {
        label "${AGENT}"
    }

    stages {
        stage("Continuous Integration / Intégration Continue") {
            steps {
                git branch: "main", url: "https://github.com/Elvi-2202/my-bank-bakend.git"
                sh "composer install"
            }
        }
        stage("Continuous Delivery / Livraison Continue") {
            steps {
                sh "docker build . -t ${DOCKERHUB_USERNAME}/my-bank-backend"
                sh "docker login -u ${DOCKERHUB_USERNAME} -p ${DOCKER_PASSWORD}" // Créer un PAT sur Docker Hub : https://app.docker.com/settings/personal-access-tokens
                sh "docker push ${DOCKERHUB_USERNAME}/my-bank-backend"
            }
        }
    }
}