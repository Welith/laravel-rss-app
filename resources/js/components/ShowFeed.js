import React, {Component} from "react";
import {Link} from 'react-router-dom';
import swal from "sweetalert";

class ShowFeed extends Component {


    feed_id = this.props.match.params.id;

    state = {
        title: '',
        link: '',
        source: '',
        source_url: '',
        publish_date: '',
        description: ''
    }

    handleInput = (e) => {

        this.setState({

            [e.target.name]: e.target.value
        });
    }

    async componentDidMount(){

        const res = await axios.get(`/api/feeds/${this.feed_id}`)

        if (res.data.status === 200) {

            this.setState({

                title: res.data.feed.title,
                link: res.data.feed.link,
                source: res.data.feed.source,
                source_url: res.data.feed.source_url,
                publish_date: res.data.feed.publish_date,
                description: res.data.feed.description
            });
        } else {

            swal({
                title: "Error!",
                text: res.data.message,
                icon: "error",
                button: "OK",
            });
        }
    }

    render() {

        return(
            <div className="container align-items-center justify-content-center">
                <div className="row align-items-center justify-content-center">
                    <div className="col-md-6 align-items-center justify-content-center">
                        <div className="card">
                            <div className="card-header text-center">
                                <h4>Feed # {this.feed_id}
                                    <Link to={'/'} className="btn btn-primary btn-sm float-right"><i className="fas fa-long-arrow-alt-left"></i> Back</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                    <div className="form-group mb-3">
                                        <h5>Title</h5>
                                        <p className="text-justify">{this.state.title}</p>
                                    </div>
                                    <div className="form-group mb-3">
                                        <h5>Link</h5>
                                        <p className="text-justify">{this.state.link}</p>
                                    </div>
                                    <div className="form-group mb-3">
                                        <h5>Source</h5>
                                        <p className="text-justify">{this.state.source}</p>
                                    </div>
                                    <div className="form-group mb-3">
                                        <h5>Source URL</h5>
                                        <p className="text-justify">{this.state.source_url}</p>
                                    </div>
                                    <div className="form-group mb-3">
                                        <h5>Publish Date</h5>
                                        <p className="text-justify">{this.state.publish_date}</p>
                                    </div>
                                    <div className="form-group mb-3">
                                        <h5>Description</h5>
                                        <p className="text-justify">{this.state.description}</p>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default ShowFeed;


